<?php
/**
 * Yalnızca yerel geliştirme içindir (PHP yerleşik sunucusu):
 *   php -S localhost:8000 dev-router.php
 * Canlı sunucuda .htaccess kullanılır; bu dosyaya web erişimi kapalıdır.
 */
if (PHP_SAPI !== 'cli-server') {
    http_response_code(404);
    exit;
}
$path = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// .htaccess'teki erişim kısıtlamalarının karşılığı
if (preg_match('#^/(app|config|database|templates|bin|admin/views)(/|$)#', $path)
    || preg_match('#^/admin/(lib|resources)\.php$#', $path)
    || preg_match('#(^|/)\.#', $path)
    || preg_match('#\.(sql|md|log|ini|sh)$#i', $path)
    || preg_match('#^/uploads/.*\.(php\d?|phtml|phar|html?|svg)$#i', $path)
    || $path === '/dev-router.php') {
    http_response_code(403);
    echo 'Erişim engellendi.';
    return true;
}

$file = __DIR__ . $path;
if ($path === '/admin' ) {
    header('Location: /admin/');
    return true;
}
if ($path === '/admin/' || $path === '/admin/index.php') {
    $_SERVER['SCRIPT_NAME'] = '/admin/index.php';
    require __DIR__ . '/admin/index.php';
    return true;
}
if ($path !== '/' && is_file($file)) {
    return false; // Statik dosyayı yerleşik sunucu verir
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
