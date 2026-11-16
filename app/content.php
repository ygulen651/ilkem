<?php
declare(strict_types=1);

/* Ziyaretçi tarafı içerik sorguları ve iletişim formu */

const PROJECT_STATUSES = ['devam' => 'Devam ediyor', 'tamamlandi' => 'Tamamlandı', 'planlaniyor' => 'Planlanıyor'];
const POST_TYPES = ['haber' => 'Haber', 'duyuru' => 'Duyuru'];

function published_slides(): array
{
    return db_all('SELECT * FROM hero_slides WHERE is_published = 1 ORDER BY sort_order, id');
}

function published_activities(?int $limit = null, ?int $exceptId = null): array
{
    $sql = 'SELECT * FROM activities WHERE is_published = 1' . ($exceptId ? ' AND id <> ' . (int) $exceptId : '') . ' ORDER BY sort_order, id';
    return db_all($sql . ($limit ? ' LIMIT ' . (int) $limit : ''));
}

function find_activity(string $slug): ?array
{
    return db_one('SELECT * FROM activities WHERE slug = ? AND is_published = 1', [$slug]);
}

function published_projects(?string $status = null, ?int $limit = null, ?int $exceptId = null): array
{
    $sql = 'SELECT * FROM projects WHERE is_published = 1';
    $params = [];
    if ($status !== null) {
        $sql .= ' AND status = ?';
        $params[] = $status;
    }
    if ($exceptId) {
        $sql .= ' AND id <> ' . (int) $exceptId;
    }
    $sql .= ' ORDER BY sort_order, COALESCE(start_date, created_at) DESC, id DESC';
    return db_all($sql . ($limit ? ' LIMIT ' . (int) $limit : ''), $params);
}

function featured_projects(int $limit = 3): array
{
    $rows = db_all('SELECT * FROM projects WHERE is_published = 1 AND is_featured = 1 ORDER BY sort_order, id DESC LIMIT ' . $limit);
    return $rows !== [] ? $rows : published_projects(null, $limit);
}

function find_project(string $slug): ?array
{
    return db_one('SELECT * FROM projects WHERE slug = ? AND is_published = 1', [$slug]);
}

/** Yayındaki haber/duyurular için ortak koşul. */
const POST_VISIBLE = 'is_published = 1 AND published_at <= NOW()';

function count_posts(?string $type = null): int
{
    return $type
        ? (int) db_value('SELECT COUNT(*) FROM posts WHERE ' . POST_VISIBLE . ' AND type = ?', [$type])
        : (int) db_value('SELECT COUNT(*) FROM posts WHERE ' . POST_VISIBLE);
}

function latest_posts(?string $type = null, int $limit = 3, int $offset = 0, ?int $exceptId = null): array
{
    $sql = 'SELECT * FROM posts WHERE ' . POST_VISIBLE;
    $params = [];
    if ($type) {
        $sql .= ' AND type = ?';
        $params[] = $type;
    }
    if ($exceptId) {
        $sql .= ' AND id <> ' . (int) $exceptId;
    }
    $sql .= ' ORDER BY published_at DESC, id DESC LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;
    return db_all($sql, $params);
}

function find_post(string $slug): ?array
{
    return db_one('SELECT * FROM posts WHERE slug = ? AND ' . POST_VISIBLE, [$slug]);
}

function count_gallery(): int
{
    return (int) db_value('SELECT COUNT(*) FROM gallery WHERE is_published = 1');
}

function gallery_items(int $limit, int $offset = 0): array
{
    return db_all('SELECT * FROM gallery WHERE is_published = 1 ORDER BY sort_order, id DESC LIMIT ' . $limit . ' OFFSET ' . $offset);
}

/** @return array<string,string> Ağ adı => URL */
function social_links(): array
{
    $out = [];
    foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'twitter' => 'X (Twitter)', 'youtube' => 'YouTube', 'linkedin' => 'LinkedIn', 'whatsapp' => 'WhatsApp'] as $key => $label) {
        $value = setting('social_' . $key);
        if ($value !== '' && preg_match('#^https://#i', $value)) {
            $out[$key] = $value;
        }
    }
    return $out;
}

function social_label(string $key): string
{
    return ['facebook' => 'Facebook', 'instagram' => 'Instagram', 'twitter' => 'X (Twitter)', 'youtube' => 'YouTube', 'linkedin' => 'LinkedIn', 'whatsapp' => 'WhatsApp'][$key] ?? $key;
}

/* ------------------------------------------------------------------
 * İletişim formu
 * ------------------------------------------------------------------ */

const CONTACT_SUBJECTS = [
    'genel'     => 'Genel bilgi',
    'gonullu'   => 'Gönüllülük başvurusu',
    'uyelik'    => 'Üyelik',
    'is-birligi'=> 'İş birliği / proje önerisi',
    'bagis'     => 'Destek ve bağış',
    'diger'     => 'Diğer',
];

/**
 * İletişim formunu işler. Başarılıysa true; hatalar ve eski değerler oturuma yazılır.
 */
function handle_contact_submission(): bool
{
    $old = [
        'name'    => mb_substr(input('name'), 0, 200),
        'email'   => mb_substr(input('email'), 0, 200),
        'phone'   => mb_substr(input('phone'), 0, 40),
        'subject' => input('subject'),
        'message' => mb_substr(input('message'), 0, 6000),
    ];
    $errors = [];

    if (!csrf_valid()) {
        $errors['form'] = 'Oturum süreniz dolmuş olabilir. Lütfen formu tekrar gönderin.';
    }

    // Spam önlemi 1: gizli alan (insanlar görmez, botlar doldurur)
    if (input('website') !== '') {
        unset($_SESSION['contact_old'], $_SESSION['contact_errors']);
        return true; // Botu fark ettirmeden sessizce yok say
    }

    // Spam önlemi 2: form çok hızlı gönderildiyse
    $renderedAt = (int) ($_SESSION['contact_rendered_at'] ?? 0);
    if ($renderedAt === 0 || time() - $renderedAt < 3) {
        $errors['form'] = 'Form çok hızlı gönderildi. Lütfen birkaç saniye bekleyip tekrar deneyin.';
    }

    // Spam önlemi 3: aynı IP'den saatlik sınır
    $recent = (int) db_value('SELECT COUNT(*) FROM messages WHERE ip = ? AND created_at > (NOW() - INTERVAL 1 HOUR)', [client_ip()]);
    if ($recent >= 5) {
        $errors['form'] = 'Kısa süre içinde çok sayıda mesaj gönderdiniz. Lütfen daha sonra tekrar deneyin.';
    }

    if (mb_strlen($old['name']) < 2 || mb_strlen($old['name']) > 100) {
        $errors['name'] = 'Lütfen adınızı ve soyadınızı yazın.';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($old['email']) > 150) {
        $errors['email'] = 'Geçerli bir e-posta adresi girin.';
    }
    if ($old['phone'] !== '' && !preg_match('/^[0-9 +()\-]{7,20}$/', $old['phone'])) {
        $errors['phone'] = 'Telefon numarası yalnızca rakam, boşluk ve + ( ) - içerebilir.';
    }
    if (!isset(CONTACT_SUBJECTS[$old['subject']])) {
        $errors['subject'] = 'Lütfen bir konu seçin.';
    }
    $len = mb_strlen($old['message']);
    if ($len < 10) {
        $errors['message'] = 'Mesajınız en az 10 karakter olmalı.';
    } elseif ($len > 5000) {
        $errors['message'] = 'Mesajınız en fazla 5000 karakter olabilir.';
    } elseif (preg_match_all('#https?://#i', $old['message']) > 3) {
        $errors['message'] = 'Mesajınızda çok fazla bağlantı var.';
    }
    if (empty($_POST['consent'])) {
        $errors['consent'] = 'Devam etmek için kişisel verilerin işlenmesine onay vermelisiniz.';
    }

    if ($errors !== []) {
        $_SESSION['contact_old'] = $old;
        $_SESSION['contact_errors'] = $errors;
        return false;
    }

    db_exec(
        'INSERT INTO messages (name, email, phone, subject, message, ip, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [$old['name'], $old['email'], $old['phone'], CONTACT_SUBJECTS[$old['subject']], $old['message'], client_ip(), mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255)]
    );
    notify_new_message($old);
    unset($_SESSION['contact_old'], $_SESSION['contact_errors'], $_SESSION['contact_rendered_at']);
    return true;
}

/** İsteğe bağlı e-posta bildirimi (config: mail.notify). */
function notify_new_message(array $m): void
{
    $to = (string) config('mail.to', '');
    $from = (string) config('mail.from', '');
    if (!config('mail.notify', false) || !filter_var($to, FILTER_VALIDATE_EMAIL) || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
        return;
    }
    $clean = static fn (string $s): string => str_replace(["\r", "\n"], ' ', $s);
    $subject = '=?UTF-8?B?' . base64_encode('Yeni iletişim mesajı: ' . $clean(CONTACT_SUBJECTS[$m['subject']] ?? '')) . '?=';
    $body = "Ad Soyad: {$m['name']}\nE-posta: {$m['email']}\nTelefon: {$m['phone']}\n\n{$m['message']}\n\nYönetim paneli: " . absolute_url('admin/?r=messages');
    $headers = implode("\r\n", [
        'From: ' . $from,
        'Reply-To: ' . $clean($m['email']),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ]);
    @mail($to, $subject, $body, $headers);
}
