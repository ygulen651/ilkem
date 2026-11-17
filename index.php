<?php
declare(strict_types=1);

/**
 * İlkem Derneği - ön yüz yönlendiricisi.
 * .htaccess tüm temiz URL'leri bu dosyaya yönlendirir.
 */

define('ENTRY_DEPTH', 0);
require __DIR__ . '/app/bootstrap.php';

if (!APP_CONFIGURED) {
    http_response_code(503);
    render_fatal_page('Kurulum bekleniyor', 'config/config.example.php dosyasını config/config.php olarak kopyalayıp veritabanı bilgilerini girin. Ayrıntılar README.md dosyasındadır.');
    exit;
}

try {
    db();
    settings();
} catch (PDOException $e) {
    error_log('[ilkem] Veritabanı hatası: ' . $e->getMessage());
    http_response_code(503);
    render_fatal_page('Veritabanına bağlanılamadı', 'Site geçici olarak hizmet veremiyor. Yöneticiyseniz config/config.php içindeki veritabanı bilgilerini ve SQL dosyalarının içe aktarıldığını kontrol edin.', config('app.debug', false) ? $e->getMessage() : '');
    exit;
}

$path = current_path();
$segments = $path === '' ? [] : explode('/', $path);
$section = $segments[0] ?? '';
$slug = $segments[1] ?? null;

if (count($segments) > 2 || ($slug !== null && !preg_match('/^[a-z0-9\-]{1,180}$/', $slug))) {
    not_found();
}

/** 404 sayfası. */
function not_found(): never
{
    render('404', ['pageTitle' => 'Sayfa bulunamadı'], 404);
    exit;
}

function page_number(): int
{
    $p = query('sayfa', '1');
    return ctype_digit($p) ? max(1, (int) $p) : 1;
}

switch ($section) {
    case '':
        render('home', [
            'slides'        => published_slides(),
            'announcements' => latest_posts('duyuru', 3),
            'activities'    => published_activities(6),
            'projects'      => featured_projects(3),
            'posts'         => latest_posts(null, 3),
            'gallery'       => gallery_items(6),
            'activeNav'     => 'home',
        ]);
        break;

    case 'hakkimizda':
        if ($slug !== null) {
            not_found();
        }
        render('about', ['pageTitle' => setting('about_title', 'Hakkımızda'), 'activeNav' => 'about', 'activities' => published_activities(3)]);
        break;

    case 'faaliyetlerimiz':
        if ($slug === null) {
            render('activities', ['pageTitle' => 'Faaliyetlerimiz', 'activeNav' => 'activities', 'activities' => published_activities()]);
            break;
        }
        $item = find_activity($slug) ?? not_found();
        render('activity', [
            'pageTitle' => $item['title'], 'activeNav' => 'activities', 'item' => $item,
            'metaDescription' => excerpt($item['summary'] ?: $item['content'], 160),
            'ogImage' => $item['image'], 'others' => published_activities(4, (int) $item['id']),
        ]);
        break;

    case 'projeler':
        if ($slug === null) {
            $status = query('durum');
            $status = isset(PROJECT_STATUSES[$status]) ? $status : null;
            render('projects', ['pageTitle' => 'Projeler', 'activeNav' => 'projects', 'status' => $status, 'projects' => published_projects($status)]);
            break;
        }
        $item = find_project($slug) ?? not_found();
        render('project', [
            'pageTitle' => $item['title'], 'activeNav' => 'projects', 'item' => $item,
            'metaDescription' => excerpt($item['summary'] ?: $item['content'], 160),
            'ogImage' => $item['image'], 'others' => published_projects(null, 3, (int) $item['id']),
        ]);
        break;

    case 'duyurular':
        if ($slug !== null) {
            not_found();
        }
        redirect(url('haberler') . '?tur=duyuru', 301);

    case 'haberler':
        if ($slug === null) {
            $type = query('tur');
            $type = isset(POST_TYPES[$type]) ? $type : null;
            $pager = paginate(count_posts($type), 9, page_number());
            render('posts', [
                'pageTitle' => $type === 'duyuru' ? 'Duyurular' : ($type === 'haber' ? 'Haberler' : 'Haberler & Duyurular'),
                'activeNav' => 'posts', 'type' => $type, 'pager' => $pager,
                'posts' => latest_posts($type, $pager['limit'], $pager['offset']),
            ]);
            break;
        }
        $item = find_post($slug) ?? not_found();
        render('post', [
            'pageTitle' => $item['title'], 'activeNav' => 'posts', 'item' => $item,
            'metaDescription' => excerpt($item['summary'] ?: $item['content'], 160),
            'ogImage' => $item['image'], 'others' => latest_posts(null, 3, 0, (int) $item['id']),
        ]);
        break;

    case 'galeri':
        if ($slug !== null) {
            not_found();
        }
        $pager = paginate(count_gallery(), 30, page_number());
        render('gallery', ['pageTitle' => 'Fotoğraf Galerisi', 'activeNav' => 'gallery', 'pager' => $pager, 'items' => gallery_items($pager['limit'], $pager['offset'])]);
        break;

    case 'iletisim':
        if ($slug !== null) {
            not_found();
        }
        if (is_post()) {
            if (handle_contact_submission()) {
                flash('success', 'Mesajınız bize ulaştı. En kısa sürede size dönüş yapacağız. Teşekkür ederiz!');
            }
            redirect(url('iletisim') . '#form');
        }
        $_SESSION['contact_rendered_at'] = time();
        $errors = $_SESSION['contact_errors'] ?? [];
        $old = $_SESSION['contact_old'] ?? [];
        unset($_SESSION['contact_errors'], $_SESSION['contact_old']);
        $preset = query('konu');
        if (!isset($old['subject']) && isset(CONTACT_SUBJECTS[$preset])) {
            $old['subject'] = $preset;
        }
        render('contact', ['pageTitle' => 'İletişim', 'activeNav' => 'contact', 'errors' => $errors, 'old' => $old]);
        break;

    default:
        not_found();
}
