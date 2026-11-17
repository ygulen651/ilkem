<?php
declare(strict_types=1);

/* ------------------------------------------------------------------
 * Yapılandırma
 * ------------------------------------------------------------------ */

function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['__config'] ?? null;
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

/* ------------------------------------------------------------------
 * Çıktı ve URL yardımcıları
 * ------------------------------------------------------------------ */

/** HTML çıktısı için kaçışlama. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443')
        || (strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https');
}

/** Sitenin kök yolu (alt klasör kurulumlarını destekler), sonda eğik çizgi olmadan. */
function base_path(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $configured = trim((string) config('app.base_url', ''));
    if ($configured !== '') {
        $path = (string) (parse_url($configured, PHP_URL_PATH) ?? '');
        return $base = rtrim($path, '/');
    }
    if (PHP_SAPI === 'cli-server' || PHP_SAPI === 'cli') {
        return $base = '';
    }
    $dir = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php')));
    for ($i = 0; $i < ENTRY_DEPTH; $i++) {
        $dir = str_replace('\\', '/', dirname($dir));
    }
    $dir = rtrim($dir, '/.');
    return $base = $dir;
}

function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return base_path() . '/' . $path;
}

/** Tam (alan adıyla) URL. */
function absolute_url(string $path = ''): string
{
    $configured = trim((string) config('app.base_url', ''));
    if ($configured !== '') {
        $origin = preg_replace('#^(https?://[^/]+).*$#i', '$1', $configured);
    } else {
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
        if (!preg_match('/^[a-z0-9.\-:\[\]]+$/i', $host)) {
            $host = 'localhost';
        }
        $origin = (is_https() ? 'https://' : 'http://') . $host;
    }
    return $origin . url($path);
}

/** Statik dosya adresi, önbellek için sürüm parametresiyle. */
function asset(string $path): string
{
    $file = ROOT_PATH . '/' . ltrim($path, '/');
    $version = is_file($file) ? (string) filemtime($file) : '1';
    return url($path) . '?v=' . $version;
}

/** Veritabanındaki görsel yolunu URL'ye çevirir; dosya yoksa yedek görsel döner. */
function media_url(?string $path, string $fallback = 'assets/img/placeholder.svg'): string
{
    $path = trim((string) $path);
    if ($path !== '' && preg_match('#^https://#i', $path)) {
        return $path;
    }
    $path = ltrim($path, '/');
    if ($path === '' || str_contains($path, '..') || !is_file(ROOT_PATH . '/' . $path)) {
        $path = $fallback;
    }
    return url($path);
}

function has_media(?string $path): bool
{
    $path = ltrim(trim((string) $path), '/');
    return $path !== '' && (preg_match('#^https://#i', $path) || (!str_contains($path, '..') && is_file(ROOT_PATH . '/' . $path)));
}

/** Yönetim panelinden girilen bağlantıları güvenli URL'ye çevirir. */
function link_url(?string $value, string $fallback = ''): string
{
    $value = trim((string) $value);
    if ($value === '') {
        return $fallback === '' ? url() : link_url($fallback);
    }
    if (preg_match('#^(https?://|mailto:|tel:)#i', $value)) {
        return $value;
    }
    if ($value[0] === '#') {
        return $value;
    }
    if (preg_match('#^[a-z][a-z0-9+.\-]*:#i', $value)) {
        return url(); // javascript: vb. şemalar reddedilir
    }
    return url($value);
}

function is_external_link(string $value): bool
{
    return (bool) preg_match('#^https?://#i', trim($value));
}

function current_path(): string
{
    static $path = null;
    if ($path !== null) {
        return $path;
    }
    $uri = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
    $uri = rawurldecode($uri);
    $base = base_path();
    if ($base !== '' && str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base));
    }
    $uri = trim($uri, '/');
    if ($uri === 'index.php') {
        $uri = '';
    }
    return $path = $uri;
}

function redirect(string $to, int $code = 303): never
{
    header('Location: ' . $to, true, $code);
    exit;
}

function client_ip(): string
{
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function input(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

function query(string $key, string $default = ''): string
{
    $value = $_GET[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

/* ------------------------------------------------------------------
 * Güvenlik başlıkları ve oturum
 * ------------------------------------------------------------------ */

function send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data: https:; media-src 'self' https:; "
        . "style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self'; "
        . "frame-src https://www.youtube-nocookie.com https://www.youtube.com https://www.google.com https://maps.google.com; "
        . "connect-src 'self'; form-action 'self'; base-uri 'self'; frame-ancestors 'self'; object-src 'none'");
    header_remove('X-Powered-By');
}

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    session_name('ilkem_oturum');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => base_path() . '/',
        'secure'   => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    $token = $_POST['_csrf'] ?? '';
    return is_string($token) && $token !== '' && hash_equals(csrf_token(), $token);
}

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

/** @return array<int, array{type:string,message:string}> */
function take_flashes(): array
{
    $items = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return is_array($items) ? $items : [];
}

/* ------------------------------------------------------------------
 * Veritabanı ve ayarlar
 * ------------------------------------------------------------------ */

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    if (!APP_CONFIGURED) {
        throw new RuntimeException('Yapılandırma dosyası (config/config.php) bulunamadı.');
    }
    $socket = trim((string) config('db.socket', ''));
    $dsn = $socket !== ''
        ? sprintf('mysql:unix_socket=%s;dbname=%s;charset=utf8mb4', $socket, (string) config('db.name'))
        : sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', (string) config('db.host', 'localhost'), (int) config('db.port', 3306), (string) config('db.name'));
    $pdo = new PDO($dsn, (string) config('db.user'), (string) config('db.pass'), [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    $offset = (new DateTime('now'))->format('P');
    $pdo->exec("SET time_zone = " . $pdo->quote($offset));
    return $pdo;
}

/** @param array<int|string, mixed> $params */
function db_all(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/** @param array<int|string, mixed> $params */
function db_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

/** @param array<int|string, mixed> $params */
function db_value(string $sql, array $params = []): mixed
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $value = $stmt->fetchColumn();
    return $value === false ? null : $value;
}

/** @param array<int|string, mixed> $params */
function db_exec(string $sql, array $params = []): int
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/** @return array<string,string> */
function settings(bool $refresh = false): array
{
    static $cache = null;
    if ($cache === null || $refresh) {
        $cache = [];
        foreach (db_all('SELECT setting_key, setting_value FROM settings') as $row) {
            $cache[$row['setting_key']] = (string) $row['setting_value'];
        }
    }
    return $cache;
}

/** Ayar değeri; boşsa varsayılan döner. */
function setting(string $key, string $default = ''): string
{
    $value = trim(settings()[$key] ?? '');
    return $value !== '' ? $value : $default;
}

function save_setting(string $key, string $value): void
{
    db_exec(
        'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
        [$key, $value]
    );
}

function site_name(): string
{
    return setting('site_name', 'İlkem Derneği');
}

/** Kurumun açık adı (ör. İlk Eğitim Müzesi Derneği). */
function site_full_name(): string
{
    return setting('site_full_name', 'İlk Eğitim Müzesi Derneği');
}

/**
 * Logo yanındaki yazı: büyük ana kelime + altında küçük satır.
 * Açık ad tanımlıysa alt satırda o gösterilir.
 * @return array{0:string,1:string,2:bool} [ana yazı, alt yazı, alt yazı uzun mu]
 */
function brand_parts(): array
{
    $parts = preg_split('/\s+/u', site_name(), 2) ?: [site_name()];
    $sub = setting('site_full_name') !== '' ? setting('site_full_name') : ($parts[1] ?? '');
    return [$parts[0], $sub, mb_strlen($sub) > 12];
}

/* ------------------------------------------------------------------
 * Metin yardımcıları
 * ------------------------------------------------------------------ */

/**
 * Düz metni güvenli HTML'e çevirir.
 * - Boş satır: yeni paragraf
 * - "## " ile başlayan satır: ara başlık
 * - "- " ile başlayan satırlar: madde listesi
 * - **kalın** yazı desteklenir
 */
function render_text(?string $text): string
{
    $text = trim(str_replace(["\r\n", "\r"], "\n", (string) $text));
    if ($text === '') {
        return '';
    }
    $inline = static fn (string $s): string => preg_replace('/\*\*(.+?)\*\*/u', '<strong>$1</strong>', e($s)) ?? e($s);
    $html = '';
    foreach (preg_split("/\n\s*\n/", $text) ?: [] as $block) {
        $lines = array_values(array_filter(array_map('trim', explode("\n", $block)), static fn ($l) => $l !== ''));
        if ($lines === []) {
            continue;
        }
        if (count($lines) === 1 && str_starts_with($lines[0], '## ')) {
            $html .= '<h3>' . $inline(substr($lines[0], 3)) . '</h3>';
            continue;
        }
        $isList = array_reduce($lines, static fn ($carry, $l) => $carry && preg_match('/^[-*•] /u', $l), true);
        if ($isList) {
            $html .= '<ul>';
            foreach ($lines as $line) {
                $html .= '<li>' . $inline(preg_replace('/^[-*•] /u', '', $line) ?? $line) . '</li>';
            }
            $html .= '</ul>';
            continue;
        }
        $html .= '<p>' . implode('<br>', array_map($inline, $lines)) . '</p>';
    }
    return $html;
}

function excerpt(?string $text, int $length = 160): string
{
    $text = trim(preg_replace('/\s+/u', ' ', str_replace(['**', '## '], '', (string) $text)) ?? '');
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    $cut = mb_substr($text, 0, $length);
    $space = mb_strrpos($cut, ' ');
    return rtrim(mb_substr($cut, 0, $space !== false && $space > $length * 0.6 ? $space : $length), " ,.;:-") . '…';
}

function format_date(?string $date, bool $withTime = false): string
{
    if (!$date) {
        return '';
    }
    $ts = strtotime($date);
    if ($ts === false) {
        return '';
    }
    $months = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
    $out = date('j', $ts) . ' ' . $months[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);
    return $withTime ? $out . ', ' . date('H:i', $ts) : $out;
}

function slugify(string $text): string
{
    $map = [
        'ç' => 'c', 'Ç' => 'c', 'ğ' => 'g', 'Ğ' => 'g', 'ı' => 'i', 'İ' => 'i', 'I' => 'i',
        'ö' => 'o', 'Ö' => 'o', 'ş' => 's', 'Ş' => 's', 'ü' => 'u', 'Ü' => 'u',
        'â' => 'a', 'Â' => 'a', 'î' => 'i', 'Î' => 'i', 'û' => 'u', 'Û' => 'u',
    ];
    $text = mb_strtolower(strtr($text, $map));
    $text = (string) preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim(substr($text, 0, 150), '-');
    return $text !== '' ? $text : 'icerik';
}

/** YouTube bağlantısından video kimliğini çıkarır. */
function youtube_id(?string $url): ?string
{
    $url = trim((string) $url);
    if ($url === '') {
        return null;
    }
    if (preg_match('~(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $m)) {
        return $m[1];
    }
    return null;
}

/** MP4/WEBM dosya adresini (site içi yol veya https) güvenli URL'ye çevirir; video dosyası değilse null döner. */
function video_file_url(?string $value): ?string
{
    $value = trim((string) $value);
    if ($value === '' || !preg_match('~\.(mp4|webm)$~i', (string) parse_url($value, PHP_URL_PATH))) {
        return null;
    }
    if (preg_match('#^https://#i', $value)) {
        return $value;
    }
    $path = ltrim($value, '/');
    return str_contains($path, '..') || preg_match('#^[a-z][a-z0-9+.\-]*:#i', $path) ? null : url($path);
}

/** Tanıtım videosu ayarı geçerli bir YouTube bağlantısı veya video dosyası mı? */
function has_promo_video(): bool
{
    $value = setting('home_video_url');
    return youtube_id($value) !== null || video_file_url($value) !== null;
}

/** Yalnızca Google Haritalar gömme adreslerine izin verir. */
function safe_map_embed(?string $url): ?string
{
    $url = trim((string) $url);
    if (preg_match('~^https://(www\.)?google\.com/maps/embed\?[^"\'<>\s]+$~', $url)) {
        return $url;
    }
    return null;
}

function tel_href(string $phone): string
{
    return 'tel:' . preg_replace('/[^0-9+]/', '', $phone);
}

/* ------------------------------------------------------------------
 * Sayfalama
 * ------------------------------------------------------------------ */

/** @return array{page:int,pages:int,offset:int,limit:int,total:int} */
function paginate(int $total, int $perPage, int $page): array
{
    $pages = max(1, (int) ceil($total / max(1, $perPage)));
    $page = min(max(1, $page), $pages);
    return ['page' => $page, 'pages' => $pages, 'offset' => ($page - 1) * $perPage, 'limit' => $perPage, 'total' => $total];
}

/* ------------------------------------------------------------------
 * Şablon işleme
 * ------------------------------------------------------------------ */

/** @param array<string, mixed> $data */
function partial(string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require ROOT_PATH . '/templates/partials/' . $name . '.php';
}

/**
 * Sayfa şablonunu ortak üst/alt bölümle birlikte işler.
 * @param array<string, mixed> $data
 */
function render(string $template, array $data = [], int $status = 200): void
{
    http_response_code($status);
    $data += [
        'pageTitle'       => '',
        'metaDescription' => setting('site_description', 'İlkem Derneği (İlk Eğitim Müzesi Derneği); ilköğretim tarihimizi ve okul hatıralarını koruyan, sergileyen bir dernektir.'),
        'activeNav'       => '',
        'ogImage'         => '',
    ];
    extract($data, EXTR_SKIP);
    require ROOT_PATH . '/templates/layout/header.php';
    require ROOT_PATH . '/templates/pages/' . $template . '.php';
    require ROOT_PATH . '/templates/layout/footer.php';
}

/** Veritabanından bağımsız, sade hata sayfası. */
function render_fatal_page(string $title, string $message, string $detail = ''): void
{
    $css = ROOT_PATH . '/assets/css/style.css';
    $cssUrl = is_file($css) ? asset('assets/css/style.css') : '';
    echo '<!doctype html><html lang="tr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>' . e($title) . '</title>';
    if ($cssUrl) {
        echo '<link rel="stylesheet" href="' . e($cssUrl) . '">';
    }
    echo '</head><body class="fatal-page"><main class="fatal-box"><img src="' . e(url('assets/img/logo.svg')) . '" width="56" height="56" alt="">';
    echo '<h1>' . e($title) . '</h1><p>' . e($message) . '</p>';
    if ($detail !== '') {
        echo '<pre>' . e($detail) . '</pre>';
    }
    echo '<a class="button" href="' . e(url()) . '">Ana sayfaya dön</a></main></body></html>';
}
