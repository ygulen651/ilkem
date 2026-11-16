<?php
declare(strict_types=1);

/* Görsel yükleme: tür, boyut ve içerik doğrulaması */

const ALLOWED_IMAGE_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
];

function upload_max_bytes(): int
{
    return max(1, (int) config('app.upload_max_mb', 5)) * 1024 * 1024;
}

/**
 * Tek veya çoklu dosya alanını düz bir listeye çevirir.
 * @return array<int, array{name:string,type:string,tmp_name:string,error:int,size:int}>
 */
function uploaded_files(string $field): array
{
    $f = $_FILES[$field] ?? null;
    if (!is_array($f) || !isset($f['error'])) {
        return [];
    }
    if (!is_array($f['error'])) {
        return [$f];
    }
    $out = [];
    foreach (array_keys($f['error']) as $i) {
        if (!is_int($f['error'][$i]) && !is_numeric($f['error'][$i])) {
            continue;
        }
        $out[] = [
            'name' => (string) $f['name'][$i], 'type' => (string) $f['type'][$i],
            'tmp_name' => (string) $f['tmp_name'][$i], 'error' => (int) $f['error'][$i], 'size' => (int) $f['size'][$i],
        ];
    }
    return $out;
}

/**
 * Yüklenen görseli doğrular ve uploads/YYYY/MM klasörüne rastgele adla kaydeder.
 * Dosya seçilmemişse null döner ve $error boş kalır.
 */
function store_uploaded_image(array $file, ?string &$error = null): ?string
{
    $error = null;
    $code = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($code === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $maxMb = (int) config('app.upload_max_mb', 5);
    if ($code === UPLOAD_ERR_INI_SIZE || $code === UPLOAD_ERR_FORM_SIZE) {
        $error = "Dosya çok büyük. En fazla {$maxMb} MB yükleyebilirsiniz.";
        return null;
    }
    if ($code !== UPLOAD_ERR_OK) {
        $error = 'Dosya yüklenemedi (hata kodu: ' . $code . ').';
        return null;
    }
    $tmp = (string) $file['tmp_name'];
    if (!is_uploaded_file($tmp)) {
        $error = 'Geçersiz dosya yüklemesi.';
        return null;
    }
    if ((int) $file['size'] > upload_max_bytes() || filesize($tmp) > upload_max_bytes()) {
        $error = "Dosya çok büyük. En fazla {$maxMb} MB yükleyebilirsiniz.";
        return null;
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp) ?: '';
    if (!isset(ALLOWED_IMAGE_TYPES[$mime])) {
        $error = 'Yalnızca JPG, PNG, WEBP veya GIF görseller yüklenebilir.';
        return null;
    }
    $info = @getimagesize($tmp);
    if ($info === false || $info[0] < 1 || $info[1] < 1 || ($info['mime'] ?? '') !== $mime) {
        $error = 'Dosya geçerli bir görsel değil.';
        return null;
    }
    if ($info[0] * $info[1] > 50_000_000) {
        $error = 'Görselin çözünürlüğü çok yüksek.';
        return null;
    }

    $relativeDir = 'uploads/' . date('Y') . '/' . date('m');
    $absoluteDir = ROOT_PATH . '/' . $relativeDir;
    if (!is_dir($absoluteDir) && !mkdir($absoluteDir, 0755, true) && !is_dir($absoluteDir)) {
        $error = 'Yükleme klasörü oluşturulamadı. uploads klasörünün yazma iznini kontrol edin.';
        return null;
    }
    $name = bin2hex(random_bytes(16)) . '.' . ALLOWED_IMAGE_TYPES[$mime];
    $target = $absoluteDir . '/' . $name;

    if (!move_uploaded_file($tmp, $target)) {
        $error = 'Dosya kaydedilemedi. uploads klasörünün yazma iznini kontrol edin.';
        return null;
    }
    @chmod($target, 0644);
    optimize_image($target, $mime);
    return $relativeDir . '/' . $name;
}

/** Çok büyük görselleri küçültür (GD varsa). Hata olursa orijinal dosya korunur. */
function optimize_image(string $path, string $mime, int $maxSide = 2200): void
{
    if ($mime === 'image/gif' || !function_exists('imagecreatetruecolor')) {
        return;
    }
    $size = @getimagesize($path);
    if ($size === false || max($size[0], $size[1]) <= $maxSide) {
        return;
    }
    $src = match ($mime) {
        'image/jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($path) : false,
        'image/png'  => function_exists('imagecreatefrompng') ? @imagecreatefrompng($path) : false,
        'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
        default      => false,
    };
    if ($src === false) {
        return;
    }
    $ratio = $maxSide / max($size[0], $size[1]);
    $w = max(1, (int) round($size[0] * $ratio));
    $h = max(1, (int) round($size[1] * $ratio));
    $dst = imagecreatetruecolor($w, $h);
    if ($mime !== 'image/jpeg') {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $size[0], $size[1]);
    $tmp = $path . '.tmp';
    $ok = match ($mime) {
        'image/jpeg' => imagejpeg($dst, $tmp, 84),
        'image/png'  => imagepng($dst, $tmp, 7),
        'image/webp' => imagewebp($dst, $tmp, 84),
        default      => false,
    };
    if ($ok) {
        rename($tmp, $path);
    } else {
        @unlink($tmp);
    }
}

/** Yalnızca uploads klasöründeki dosyaları siler; örnek görseller korunur. */
function delete_media(?string $path): void
{
    $path = ltrim(trim((string) $path), '/');
    if ($path === '' || !str_starts_with($path, 'uploads/') || str_contains($path, '..')) {
        return;
    }
    $full = realpath(ROOT_PATH . '/' . $path);
    $uploads = realpath(ROOT_PATH . '/uploads');
    if ($full !== false && $uploads !== false && str_starts_with($full, $uploads . DIRECTORY_SEPARATOR) && is_file($full)) {
        @unlink($full);
    }
}
