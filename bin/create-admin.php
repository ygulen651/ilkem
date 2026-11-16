<?php
declare(strict_types=1);

/**
 * Komut satırından yönetici hesabı oluşturur.
 * Kullanım:  php bin/create-admin.php
 * (Hostinger'da SSH ile bağlanıp public_html klasöründe çalıştırın.)
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Bu betik yalnızca komut satırından çalıştırılabilir.\n");
}

require dirname(__DIR__) . '/app/bootstrap.php';

if (!APP_CONFIGURED) {
    fwrite(STDERR, "config/config.php bulunamadı. Önce config.example.php dosyasını kopyalayıp düzenleyin.\n");
    exit(1);
}

function ask(string $label, bool $hidden = false): string
{
    echo $label;
    if ($hidden && DIRECTORY_SEPARATOR === '/' && function_exists('shell_exec')) {
        shell_exec('stty -echo');
        $value = (string) fgets(STDIN);
        shell_exec('stty echo');
        echo "\n";
    } else {
        $value = (string) fgets(STDIN);
    }
    return trim($value);
}

try {
    db();
} catch (PDOException $e) {
    fwrite(STDERR, 'Veritabanına bağlanılamadı: ' . $e->getMessage() . "\n");
    exit(1);
}

echo "İlkem Derneği - yönetici hesabı oluştur\n";
echo "Mevcut yönetici sayısı: " . admin_count() . "\n\n";

$username = ask('Kullanıcı adı: ');
$email = ask('E-posta: ');
$password = ask('Şifre (en az 10 karakter, harf ve rakam): ', true);
$confirm = ask('Şifre (tekrar): ', true);

$errors = create_admin($username, $email, $password, $confirm);
if ($errors !== []) {
    foreach ($errors as $error) {
        fwrite(STDERR, "- {$error}\n");
    }
    exit(1);
}
echo "\nYönetici hesabı oluşturuldu. /admin adresinden giriş yapabilirsiniz.\n";
