<?php
/**
 * İlkem Derneği - örnek yapılandırma dosyası
 *
 * Bu dosyayı aynı klasöre "config.php" adıyla kopyalayın ve kendi
 * bilgilerinizle doldurun. config.php dosyası web üzerinden erişime kapalıdır
 * (config/.htaccess) ve sürüm kontrolüne eklenmemelidir.
 */

return [
    'db' => [
        // Hostinger'da genellikle "localhost" kullanılır.
        'host'    => 'localhost',
        'port'    => 3306,
        // Yerel geliştirmede soket kullanmak isterseniz tam yolunu yazın, yoksa boş bırakın.
        'socket'  => '',
        'name'    => 'veritabani_adi',
        'user'    => 'veritabani_kullanicisi',
        'pass'    => 'veritabani_sifresi',
    ],

    'app' => [
        // Boş bırakılırsa otomatik algılanır. Örnek: https://www.ilkemdernegi.org
        // Site bir alt klasördeyse: https://alanadi.com/ilkem
        'base_url'  => '',

        // Canlı ortamda mutlaka false olmalı.
        'debug'     => false,

        'timezone'  => 'Europe/Istanbul',

        // HTTPS kullanılıyorsa true yapın; HTTP istekleri HTTPS'e yönlendirilir.
        'force_https' => false,

        // İlk yönetici hesabını tarayıcıdan oluşturmak için kurulum anahtarı.
        // En az 24 karakterlik rastgele bir değer girin. Örnek üretmek için:
        //   php -r "echo bin2hex(random_bytes(16)), PHP_EOL;"
        // Yönetici oluşturulduktan sonra kurulum sayfası kendiliğinden kapanır;
        // yine de bu değeri tekrar boş bırakmanız önerilir.
        'setup_token' => '',

        // Yüklenebilecek en büyük görsel boyutu (MB).
        'upload_max_mb' => 5,

        // Yönetim paneli oturumunun hareketsizlik süresi (dakika).
        'admin_idle_minutes' => 120,
    ],

    'mail' => [
        // İletişim formundan mesaj geldiğinde e-posta bildirimi gönderilsin mi?
        // Mesajlar her durumda veritabanına kaydedilir.
        'notify' => false,
        'to'     => '',            // Bildirimin gideceği adres
        'from'   => '',            // Alan adınıza ait bir adres, ör. bildirim@alanadi.com
    ],
];
