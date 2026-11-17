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
// Yerleşik sunucu Range isteklerini desteklemez; Safari video oynatmak için buna ihtiyaç duyar.
if (is_file($file) && preg_match('#\.(mp4|webm)$#i', $path)) {
    $size = filesize($file);
    $start = 0;
    $end = $size - 1;
    header('Content-Type: video/' . (str_ends_with(strtolower($path), '.webm') ? 'webm' : 'mp4'));
    header('Accept-Ranges: bytes');
    if (preg_match('/^bytes=(\d*)-(\d*)$/', $_SERVER['HTTP_RANGE'] ?? '', $m)) {
        if ($m[1] === '') {
            $start = max(0, $size - (int) $m[2]);
        } else {
            $start = (int) $m[1];
            $end = $m[2] !== '' ? min((int) $m[2], $size - 1) : $end;
        }
        if ($start > $end || $start >= $size) {
            http_response_code(416);
            header("Content-Range: bytes */$size");
            return true;
        }
        http_response_code(206);
        header("Content-Range: bytes $start-$end/$size");
    }
    header('Content-Length: ' . ($end - $start + 1));
    if ($_SERVER['REQUEST_METHOD'] !== 'HEAD') {
        $fh = fopen($file, 'rb');
        fseek($fh, $start);
        $left = $end - $start + 1;
        while ($left > 0 && !feof($fh)) {
            $chunk = fread($fh, min(1048576, $left));
            echo $chunk;
            $left -= strlen($chunk);
        }
        fclose($fh);
    }
    return true;
}
if ($path !== '/' && is_file($file)) {
    return false; // Statik dosyayı yerleşik sunucu verir
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
