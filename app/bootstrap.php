<?php
declare(strict_types=1);

/**
 * Uygulama başlangıcı: yapılandırma, hata yönetimi, oturum ve yardımcılar.
 * Her giriş noktası (index.php, admin/index.php) bu dosyayı yükler.
 * ENTRY_DEPTH: giriş dosyasının kök klasöre göre derinliği (kök = 0, admin = 1).
 */

if (PHP_VERSION_ID < 80200) {
    http_response_code(500);
    exit('Bu uygulama PHP 8.2 veya üzeri gerektirir. Mevcut sürüm: ' . PHP_VERSION);
}

define('ROOT_PATH', dirname(__DIR__));
if (!defined('ENTRY_DEPTH')) {
    define('ENTRY_DEPTH', 0);
}

mb_internal_encoding('UTF-8');

$configFile = ROOT_PATH . '/config/config.php';
$GLOBALS['__config'] = is_file($configFile) ? require $configFile : null;
define('APP_CONFIGURED', is_array($GLOBALS['__config']));

require ROOT_PATH . '/app/helpers.php';
require ROOT_PATH . '/app/icons.php';
require ROOT_PATH . '/app/auth.php';
require ROOT_PATH . '/app/upload.php';
require ROOT_PATH . '/app/content.php';

date_default_timezone_set((string) config('app.timezone', 'Europe/Istanbul'));

error_reporting(E_ALL);
ini_set('display_errors', config('app.debug', false) ? '1' : '0');
ini_set('log_errors', '1');

set_exception_handler(static function (Throwable $e): void {
    error_log('[ilkem] ' . $e::class . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }
    $detail = config('app.debug', false) ? $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() : '';
    render_fatal_page('Bir sorun oluştu', 'İsteğiniz şu anda tamamlanamadı. Lütfen biraz sonra tekrar deneyin.', $detail);
});

if (PHP_SAPI !== 'cli') {
    if (config('app.force_https', false) && !is_https()) {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if ($host !== '' && preg_match('/^[a-z0-9.\-:\[\]]+$/i', $host)) {
            header('Location: https://' . $host . ($_SERVER['REQUEST_URI'] ?? '/'), true, 301);
            exit;
        }
    }
    send_security_headers();
    start_session();
}
