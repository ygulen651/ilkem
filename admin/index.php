<?php
declare(strict_types=1);

/**
 * İlkem Derneği - yönetim paneli giriş noktası.
 * Tüm işlemler ?r=... parametresiyle yönlendirilir; giriş/kurulum dışındaki
 * her işlem oturum kontrolünden geçer, her POST işlemi CSRF doğrulaması ister.
 */

define('ENTRY_DEPTH', 1);
require dirname(__DIR__) . '/app/bootstrap.php';
require __DIR__ . '/lib.php';
require __DIR__ . '/resources.php';

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store, max-age=0');

if (!APP_CONFIGURED) {
    http_response_code(503);
    render_fatal_page('Kurulum bekleniyor', 'Önce config/config.example.php dosyasını config/config.php olarak kopyalayıp veritabanı bilgilerini girin.');
    exit;
}

try {
    db();
    $adminCount = admin_count();
} catch (PDOException $e) {
    error_log('[ilkem] Veritabanı hatası: ' . $e->getMessage());
    http_response_code(503);
    render_fatal_page('Veritabanına bağlanılamadı', 'config/config.php içindeki veritabanı bilgilerini ve schema.sql dosyasının içe aktarıldığını kontrol edin.', config('app.debug', false) ? $e->getMessage() : '');
    exit;
}

$route = query('r', 'dashboard');
$resources = admin_resources();

/* ---------------- İlk yönetici kurulumu ---------------- */
if ($adminCount === 0) {
    $token = (string) config('app.setup_token', '');
    $tokenOk = strlen($token) >= 24;
    $errors = [];
    $old = ['username' => '', 'email' => ''];
    if ($tokenOk && is_post()) {
        $old = ['username' => input('username'), 'email' => input('email')];
        if (!csrf_valid()) {
            $errors[] = 'Güvenlik doğrulaması başarısız. Sayfayı yenileyip tekrar deneyin.';
        } elseif (!hash_equals($token, (string) ($_POST['setup_token'] ?? ''))) {
            usleep(500000);
            $errors[] = 'Kurulum anahtarı hatalı.';
        } else {
            $errors = create_admin($old['username'], $old['email'], (string) ($_POST['password'] ?? ''), (string) ($_POST['password_confirm'] ?? ''));
            if ($errors === []) {
                flash('success', 'Yönetici hesabı oluşturuldu. Şimdi giriş yapabilirsiniz. Güvenlik için config.php içindeki setup_token değerini boşaltmanız önerilir.');
                redirect(admin_url('login'));
            }
        }
    }
    admin_view('setup', ['title' => 'İlk kurulum', 'tokenOk' => $tokenOk, 'errors' => $errors, 'old' => $old], 'auth');
    exit;
}

/* ---------------- Giriş / çıkış ---------------- */
if ($route === 'login') {
    if (current_admin()) {
        redirect(admin_url());
    }
    $error = null;
    $username = '';
    if (is_post()) {
        $username = mb_substr(input('username'), 0, 150);
        if (!csrf_valid()) {
            $error = 'Oturum süresi doldu. Lütfen tekrar deneyin.';
        } else {
            $result = attempt_login($username, (string) ($_POST['password'] ?? ''));
            if ($result === 'ok') {
                redirect(admin_url());
            }
            $error = $result === 'locked'
                ? 'Çok sayıda başarısız deneme yapıldı. Lütfen ' . LOGIN_WINDOW_MINUTES . ' dakika sonra tekrar deneyin.'
                : 'Kullanıcı adı veya şifre hatalı.';
        }
    }
    admin_view('login', ['title' => 'Giriş', 'error' => $error, 'username' => $username], 'auth');
    exit;
}

if ($route === 'logout') {
    if (is_post() && csrf_valid()) {
        admin_logout();
        flash('success', 'Güvenli şekilde çıkış yaptınız.');
    }
    redirect(admin_url('login'));
}

/* ---------------- Buradan sonrası oturum gerektirir ---------------- */
$admin = current_admin();
if ($admin === null) {
    redirect(admin_url('login'));
}

switch ($route) {
    case 'dashboard':
        $counts = [];
        foreach ($resources as $key => $res) {
            $counts[$key] = [
                'total' => (int) db_value('SELECT COUNT(*) FROM `' . $res['table'] . '`'),
                'published' => (int) db_value('SELECT COUNT(*) FROM `' . $res['table'] . '` WHERE is_published = 1'),
            ];
        }
        admin_view('dashboard', [
            'title' => 'Genel Bakış', 'nav' => 'dashboard', 'admin' => $admin, 'counts' => $counts, 'resources' => $resources,
            'unread' => unread_message_count(),
            'latestMessages' => db_all('SELECT id, name, subject, is_read, created_at FROM messages ORDER BY created_at DESC LIMIT 5'),
        ]);
        break;

    case 'list':
        $key = query('res');
        $res = $resources[$key] ?? null;
        if (!$res) {
            redirect(admin_url());
        }
        $where = [];
        $params = [];
        $q = mb_substr(query('q'), 0, 100);
        if ($q !== '') {
            $like = [];
            foreach ($res['search'] as $col) {
                $like[] = '`' . $col . '` LIKE ?';
                $params[] = '%' . $q . '%';
            }
            $where[] = '(' . implode(' OR ', $like) . ')';
        }
        $filter = null;
        if (!empty($res['filter'])) {
            $filter = query('f');
            if (isset($res['filter']['options'][$filter])) {
                $where[] = '`' . $res['filter']['field'] . '` = ?';
                $params[] = $filter;
            } else {
                $filter = null;
            }
        }
        $status = query('durum');
        if ($status === 'yayinda' || $status === 'taslak') {
            $where[] = 'is_published = ' . ($status === 'yayinda' ? 1 : 0);
        }
        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $total = (int) db_value('SELECT COUNT(*) FROM `' . $res['table'] . '`' . $whereSql, $params);
        $p = query('sayfa', '1');
        $pager = paginate($total, 20, ctype_digit($p) ? (int) $p : 1);
        $rows = db_all('SELECT * FROM `' . $res['table'] . '`' . $whereSql . ' ORDER BY ' . $res['order'] . ' LIMIT ' . $pager['limit'] . ' OFFSET ' . $pager['offset'], $params);
        admin_view('list', ['title' => $res['title'], 'nav' => $key, 'key' => $key, 'res' => $res, 'rows' => $rows, 'pager' => $pager, 'q' => $q, 'filter' => $filter, 'status' => $status]);
        break;

    case 'edit':
    case 'save':
        $key = query('res');
        $res = $resources[$key] ?? null;
        if (!$res) {
            redirect(admin_url());
        }
        $id = ctype_digit(query('id')) ? (int) query('id') : null;
        $record = [];
        if ($id) {
            $record = db_one('SELECT * FROM `' . $res['table'] . '` WHERE id = ?', [$id]) ?? [];
            if ($record === []) {
                flash('error', 'Kayıt bulunamadı.');
                redirect(admin_url('list', ['res' => $key]));
            }
        } else {
            foreach ($res['fields'] as $f) {
                $default = $f['default'] ?? '';
                $record[$f['name']] = $default === 'now' ? date('Y-m-d H:i:s') : $default;
            }
        }
        $errors = [];
        if ($route === 'save') {
            admin_require_csrf();
            $oldFiles = [];
            $newFiles = [];
            $values = collect_fields($res['fields'], $record, $errors, $oldFiles, $newFiles);
            if (!empty($res['slug'])) {
                $slugSource = $values['slug'] !== '' ? $values['slug'] : $values['title'];
                $values['slug'] = unique_slug($res['table'], $slugSource, $id);
            }
            if ($errors === []) {
                $cols = array_keys($values);
                if ($id) {
                    $set = implode(', ', array_map(static fn ($c) => '`' . $c . '` = ?', $cols));
                    db_exec('UPDATE `' . $res['table'] . '` SET ' . $set . ' WHERE id = ?', [...array_values($values), $id]);
                } else {
                    $placeholders = implode(', ', array_fill(0, count($cols), '?'));
                    db_exec('INSERT INTO `' . $res['table'] . '` (`' . implode('`, `', $cols) . '`) VALUES (' . $placeholders . ')', array_values($values));
                    $id = (int) db()->lastInsertId();
                }
                foreach ($oldFiles as $file) {
                    delete_media($file);
                }
                flash('success', $res['singular'] . ' kaydedildi.');
                redirect(input('after') === 'new' ? admin_url('edit', ['res' => $key]) : admin_url('edit', ['res' => $key, 'id' => $id]));
            }
            foreach ($newFiles as $file) {
                delete_media($file);
            }
            // Hatalı formda girilen değerleri koru (görseller hariç)
            foreach ($values as $k => $v) {
                $isImage = in_array($k, array_column(array_filter($res['fields'], static fn ($f) => $f['type'] === 'image'), 'name'), true);
                if (!$isImage) {
                    $record[$k] = $v;
                }
            }
        }
        admin_view('form', [
            'title' => ($id ? $res['singular'] . ' düzenle' : 'Yeni ' . mb_strtolower($res['singular'])),
            'nav' => $key, 'key' => $key, 'res' => $res, 'record' => $record, 'id' => $id, 'errors' => $errors,
        ]);
        break;

    case 'toggle':
    case 'delete':
        admin_require_csrf();
        $key = query('res');
        $res = $resources[$key] ?? null;
        $id = ctype_digit(input('id')) ? (int) input('id') : 0;
        if (!$res || !$id) {
            redirect(admin_url());
        }
        $record = db_one('SELECT * FROM `' . $res['table'] . '` WHERE id = ?', [$id]);
        if ($record) {
            if ($route === 'toggle') {
                db_exec('UPDATE `' . $res['table'] . '` SET is_published = 1 - is_published WHERE id = ?', [$id]);
                flash('success', (int) $record['is_published'] === 1 ? 'Kayıt yayından kaldırıldı (taslak).' : 'Kayıt yayına alındı.');
            } else {
                db_exec('DELETE FROM `' . $res['table'] . '` WHERE id = ?', [$id]);
                foreach ($res['fields'] as $f) {
                    if ($f['type'] === 'image') {
                        delete_media($record[$f['name']] ?? '');
                    }
                }
                flash('success', $res['singular'] . ' silindi.');
            }
        }
        $back = input('back');
        redirect($back !== '' && str_starts_with($back, url('admin/')) && !preg_match('/[\r\n]/', $back) ? $back : admin_url('list', ['res' => $key]));

    case 'bulk':
        $res = $resources['gallery'];
        if (is_post()) {
            admin_require_csrf();
            $files = array_values(array_filter(uploaded_files('images'), static fn ($f) => (int) $f['error'] !== UPLOAD_ERR_NO_FILE));
            $ok = 0;
            $fail = [];
            $baseTitle = mb_substr(input('title'), 0, 180);
            foreach (array_slice($files, 0, 30) as $i => $file) {
                $err = null;
                $path = store_uploaded_image($file, $err);
                if ($path) {
                    $title = $baseTitle !== '' ? $baseTitle . (count($files) > 1 ? ' ' . ($i + 1) : '') : '';
                    db_exec('INSERT INTO gallery (title, image, sort_order, is_published) VALUES (?, ?, 0, ?)', [$title, $path, empty($_POST['is_published']) ? 0 : 1]);
                    $ok++;
                } else {
                    $fail[] = mb_substr($file['name'], 0, 80) . ': ' . ($err ?? 'yüklenemedi');
                }
            }
            if ($ok > 0) {
                flash('success', $ok . ' fotoğraf galeriye eklendi.');
            }
            foreach ($fail as $msg) {
                flash('error', $msg);
            }
            if ($files === []) {
                flash('error', 'Lütfen en az bir fotoğraf seçin.');
            }
            redirect($ok > 0 ? admin_url('list', ['res' => 'gallery']) : admin_url('bulk'));
        }
        admin_view('bulk', ['title' => 'Toplu fotoğraf yükle', 'nav' => 'gallery']);
        break;

    case 'settings':
        $groups = admin_settings_groups();
        $group = query('g', 'general');
        if (!isset($groups[$group])) {
            $group = 'general';
        }
        $def = $groups[$group];
        $current = settings();
        $values = [];
        foreach ($def['fields'] as $f) {
            $values[$f['name']] = $current[$f['name']] ?? (string) ($f['default'] ?? '');
        }
        $errors = [];
        if (is_post()) {
            admin_require_csrf();
            $oldFiles = [];
            $newFiles = [];
            $posted = collect_fields($def['fields'], $values, $errors, $oldFiles, $newFiles);
            if ($errors === []) {
                foreach ($posted as $k => $v) {
                    save_setting($k, (string) $v);
                }
                foreach ($oldFiles as $file) {
                    delete_media($file);
                }
                flash('success', $def['title'] . ' kaydedildi.');
                redirect(admin_url('settings', ['g' => $group]));
            }
            foreach ($newFiles as $file) {
                delete_media($file);
            }
            foreach ($posted as $k => $v) {
                if (!in_array($k, array_column(array_filter($def['fields'], static fn ($f) => $f['type'] === 'image'), 'name'), true)) {
                    $values[$k] = (string) $v;
                }
            }
        }
        admin_view('settings', ['title' => $def['title'], 'nav' => 'settings-' . $group, 'groups' => $groups, 'group' => $group, 'def' => $def, 'values' => $values, 'errors' => $errors]);
        break;

    case 'messages':
        $filter = query('f');
        $where = $filter === 'okunmamis' ? ' WHERE is_read = 0' : '';
        $total = (int) db_value('SELECT COUNT(*) FROM messages' . $where);
        $p = query('sayfa', '1');
        $pager = paginate($total, 25, ctype_digit($p) ? (int) $p : 1);
        $rows = db_all('SELECT id, name, email, subject, message, is_read, created_at FROM messages' . $where . ' ORDER BY created_at DESC, id DESC LIMIT ' . $pager['limit'] . ' OFFSET ' . $pager['offset']);
        admin_view('messages', ['title' => 'İletişim Mesajları', 'nav' => 'messages', 'rows' => $rows, 'pager' => $pager, 'filter' => $filter]);
        break;

    case 'message':
        $id = ctype_digit(query('id')) ? (int) query('id') : 0;
        $msg = db_one('SELECT * FROM messages WHERE id = ?', [$id]);
        if (!$msg) {
            flash('error', 'Mesaj bulunamadı.');
            redirect(admin_url('messages'));
        }
        if ((int) $msg['is_read'] === 0) {
            db_exec('UPDATE messages SET is_read = 1 WHERE id = ?', [$id]);
        }
        admin_view('message', ['title' => 'Mesaj', 'nav' => 'messages', 'msg' => $msg]);
        break;

    case 'message-action':
        admin_require_csrf();
        $id = ctype_digit(input('id')) ? (int) input('id') : 0;
        $action = input('action');
        if ($id && $action === 'delete') {
            db_exec('DELETE FROM messages WHERE id = ?', [$id]);
            flash('success', 'Mesaj silindi.');
        } elseif ($id && $action === 'unread') {
            db_exec('UPDATE messages SET is_read = 0 WHERE id = ?', [$id]);
            flash('success', 'Mesaj okunmadı olarak işaretlendi.');
        } elseif ($id && $action === 'read') {
            db_exec('UPDATE messages SET is_read = 1 WHERE id = ?', [$id]);
        } elseif ($action === 'read-all') {
            db_exec('UPDATE messages SET is_read = 1 WHERE is_read = 0');
            flash('success', 'Tüm mesajlar okundu olarak işaretlendi.');
        }
        redirect(admin_url('messages'));

    case 'account':
        $errors = [];
        if (is_post()) {
            admin_require_csrf();
            $row = db_one('SELECT password_hash FROM admins WHERE id = ?', [$admin['id']]);
            $currentPass = (string) ($_POST['current_password'] ?? '');
            $newPass = (string) ($_POST['new_password'] ?? '');
            if (!$row || !password_verify($currentPass, $row['password_hash'])) {
                $errors[] = 'Mevcut şifreniz hatalı.';
            } else {
                $errors = password_problems($newPass, (string) ($_POST['new_password_confirm'] ?? ''));
            }
            if ($errors === []) {
                db_exec('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($newPass, PASSWORD_DEFAULT), $admin['id']]);
                session_regenerate_id(true);
                flash('success', 'Şifreniz güncellendi.');
                redirect(admin_url('account'));
            }
        }
        admin_view('account', ['title' => 'Hesabım', 'nav' => 'account', 'admin' => $admin, 'errors' => $errors]);
        break;

    default:
        redirect(admin_url());
}
