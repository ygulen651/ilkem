# İlkem Derneği — İlk Eğitim Müzesi Derneği web sitesi

İlkem Derneği (İlk Eğitim Müzesi Derneği) için PHP + MySQL ile yazılmış, mobil uyumlu kurumsal web sitesi ve yönetim paneli. Standart PHP hostinginde ve Hostinger'da çalışır; Node.js veya sürekli çalışan bir servis gerektirmez.

Sitedeki metinler ve fotoğraflar örnektir. Tümü yönetim panelinden değiştirilebilir.

## Gereksinimler

- PHP 8.2 veya üzeri (`pdo_mysql`, `fileinfo`, `mbstring` eklentileri; görsel küçültme için `gd` önerilir)
- MySQL 5.7+ veya MariaDB 10.3+
- Apache veya LiteSpeed (`mod_rewrite` açık, `.htaccess` desteği). Hostinger'da hazır gelir.

## Klasör yapısı

```
index.php            Ön yüz yönlendiricisi (temiz URL'ler)
.htaccess            URL yönlendirme ve erişim kısıtlamaları
admin/               Yönetim paneli (/admin)
app/                 Ortak PHP kodu (veritabanı, güvenlik, yükleme, içerik)
templates/           Ortak header/footer ve sayfa şablonları
assets/              CSS, JavaScript, örnek görseller, geçici logo
config/              config.example.php (örnek yapılandırma)
database/            schema.sql (tablolar) ve seed.sql (örnek içerik)
uploads/             Panelden yüklenen görseller (PHP çalıştırma kapalı)
bin/create-admin.php Komut satırından yönetici oluşturma
dev-router.php       Yalnızca yerel geliştirme için
dist/                Eski statik tasarım taslağı (siteye yüklenmesi gerekmez)
```

## Sayfalar

| Adres | Sayfa |
|---|---|
| `/` | Ana sayfa |
| `/hakkimizda` | Hakkımızda |
| `/faaliyetlerimiz`, `/faaliyetlerimiz/{ad}` | Faaliyetler ve detay |
| `/projeler`, `/projeler/{ad}` | Projeler ve detay (`?durum=devam` vb. filtre) |
| `/haberler`, `/haberler/{ad}` | Haberler ve duyurular (`?tur=haber`, `?tur=duyuru`) |
| `/duyurular` | `/haberler?tur=duyuru` adresine yönlenir |
| `/galeri` | Fotoğraf galerisi |
| `/iletisim` | İletişim bilgileri ve form |
| `/admin/` | Yönetim paneli |

## Yerel kurulum

1. Veritabanını oluşturun (utf8mb4):

   ```sql
   CREATE DATABASE ilkem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Tabloları ve örnek içerikleri **bu sırayla** içe aktarın:

   ```bash
   mysql -u KULLANICI -p ilkem < database/schema.sql
   mysql -u KULLANICI -p ilkem < database/seed.sql
   ```

3. Yapılandırma dosyasını oluşturun ve veritabanı bilgilerini girin:

   ```bash
   cp config/config.example.php config/config.php
   ```

4. Yerel sunucuyu başlatın ve tarayıcıda açın:

   ```bash
   php -S localhost:8000 dev-router.php
   ```

   Site: http://localhost:8000 · Panel: http://localhost:8000/admin/

## İlk yönetici hesabı

Güvenlik nedeniyle sabit bir varsayılan şifre yoktur ve `seed.sql` yönetici içermez. İki yöntemden birini kullanın.

**Tarayıcıdan (önerilen):**

1. Rastgele bir kurulum anahtarı üretin:

   ```bash
   php -r "echo bin2hex(random_bytes(16)), PHP_EOL;"
   ```

2. Bu değeri `config/config.php` içindeki `'setup_token'` alanına yazın (en az 24 karakter).
3. `/admin/` adresini açın. Hiç yönetici yoksa kurulum ekranı gelir. Anahtarı, kullanıcı adını, e-postayı ve şifreyi girin. Şifre en az 10 karakter olmalı, harf ve rakam içermeli.
4. Hesap oluşunca kurulum ekranı kendiliğinden kapanır. Yine de `setup_token` değerini tekrar boş bırakın.

**Komut satırından (SSH erişimi varsa):**

```bash
php bin/create-admin.php
```

Bu komutla daha sonra ek yöneticiler de oluşturabilirsiniz.

## Hostinger'a yükleme

1. **Veritabanı:** hPanel → *Veritabanları* → *MySQL Veritabanları* bölümünden yeni bir veritabanı ve kullanıcı oluşturun. Veritabanı adını, kullanıcı adını ve şifreyi not alın (Hostinger bu adlara `u123456789_` gibi bir ön ek ekler).
2. **İçe aktarma:** Aynı sayfadan *phpMyAdmin*'e girin, veritabanını seçin. *İçe Aktar* sekmesinden önce `database/schema.sql`, sonra `database/seed.sql` dosyasını yükleyin.
3. **Dosyalar:** hPanel → *Dosya Yöneticisi* ile `public_html` klasörüne (ya da FTP ile) projedeki dosyaları yükleyin. `dist/` klasörünü yüklemenize gerek yok. Gizli dosyaların (`.htaccess`, `uploads/.htaccess`) da yüklendiğinden emin olun.
4. **Yapılandırma:** Sunucuda `config/config.example.php` dosyasını `config/config.php` olarak kopyalayıp düzenleyin:
   - `host`: `localhost`
   - `name`, `user`, `pass`: 1. adımdaki bilgiler
   - `base_url`: `https://alanadiniz.com` (boş bırakılırsa otomatik algılanır)
   - `debug`: `false`
   - `force_https`: SSL etkinse `true`
   - `setup_token`: ilk yönetici için geçici anahtar (yukarıya bakın)
5. **PHP sürümü:** hPanel → *Gelişmiş* → *PHP Yapılandırması* bölümünden PHP 8.2 veya üzerini seçin.
6. **İzinler:** `uploads/` klasörü yazılabilir olmalı (genellikle 755 yeterlidir).
7. **Yönetici:** `https://alanadiniz.com/admin/` adresinden ilk yönetici hesabını oluşturun, sonra `setup_token` değerini silin.
8. **SSL:** hPanel'den ücretsiz SSL'i etkinleştirin, ardından `force_https` değerini `true` yapın.

Site bir alt klasöre kurulursa (`alanadi.com/ilkem` gibi) `base_url` değerine alt klasörle birlikte tam adresi yazın.

## Yönetim paneli

`/admin/` adresinden şunlar yönetilir:

- **Genel ayarlar:** site adı, kurumun açık adı, slogan, logo, alt bilgi metni
- **Ana sayfa görselleri:** başlık, açıklama, iki buton, görsel veya MP4/WEBM video; birden fazlası slayt olur
- **Ana sayfa bölümleri:** bölüm başlıkları, tanıtım videosu (YouTube), gönüllü çağrısı
- **Hakkımızda:** içerik, misyon, vizyon, değerler, görsel ve sayılar
- **Faaliyetler, projeler, haberler ve duyurular:** ekleme, düzenleme, silme, yayınlama veya taslağa alma
- **Fotoğraf galerisi:** tek tek veya toplu yükleme (en fazla 30 fotoğraf)
- **İletişim bilgileri ve Google Haritalar**, sosyal medya bağlantıları
- **İletişim formundan gelen mesajlar:** okuma, okunmadı olarak işaretleme, silme
- **Hesabım:** şifre değiştirme

İçerik metinlerinde basit biçimlendirme kullanılabilir: boş satır yeni paragraf başlatır, `## ` ile başlayan satır ara başlık olur, `- ` ile başlayan satırlar madde listesi olur, `**kalın**` yazılabilir.

Büyük videoları panel yerine Dosya Yöneticisi ile `uploads/video/` klasörüne yükleyip ana sayfa görselinin video alanına `/uploads/video/dosya.mp4` yazın.

## Güvenlik önlemleri

- Şifreler `password_hash` / `password_verify` ile saklanır; sabit varsayılan şifre yoktur.
- Tüm sorgular PDO hazır ifadeleriyle (prepared statements) çalışır; tüm çıktılar kaçışlanır.
- Paneldeki ve formdaki tüm POST işlemlerinde CSRF doğrulaması yapılır.
- Paneldeki her işlem oturum kontrolünden geçer. Oturum 120 dakika hareketsizlikten sonra kapanır (`admin_idle_minutes`).
- Girişte kaba kuvvet koruması vardır: aynı IP'den 15 dakikada 6 hatalı denemeden sonra giriş geçici olarak kilitlenir.
- Yüklenen görsellerin türü (gerçek içeriğe bakarak) ve boyutu doğrulanır. Dosyalar rastgele adla kaydedilir. `uploads/` klasöründe PHP ve betik çalıştırma kapalıdır.
- İletişim formunda spam önlemleri:
  - botların doldurduğu gizli alan
  - formu çok hızlı gönderme kontrolü
  - aynı IP'den saatte en fazla 5 mesaj
  - bağlantı sayısı sınırı
- `app/`, `config/`, `database/`, `templates/` ve `bin/` klasörlerine web üzerinden erişim kapalıdır.
- Güvenlik başlıkları gönderilir: Content-Security-Policy, X-Frame-Options, nosniff.

İsterseniz `config/config.php` içinde `mail.notify` ayarını açarak yeni mesajlar için e-posta bildirimi alabilirsiniz. Bunun için Hostinger'da alan adınıza ait bir gönderen adresi gerekir.

## Test durumu

Yerelde PHP 8.5 ve MariaDB ile kontrol edilenler:

- Tüm PHP dosyalarında sözdizimi hatası yok.
- Ön yüzün tüm sayfaları, 404 sayfası ve korumalı klasörlerin 403 yanıtı.
- Masaüstü ve mobil görünüm, mobil menü, slaytlar.
- İletişim formunun sunucu tarafı doğrulaması.

Henüz test edilmemiş noktalar:

- Gerçek Apache/LiteSpeed sunucusunda `.htaccess` kuralları. Yerelde PHP'nin yerleşik sunucusu ve onun karşılığı olan `dev-router.php` kullanıldı.
- Yönetim panelinin tarayıcıdan uçtan uca testi: giriş, ekleme, düzenleme, silme, yayınlama ve görsel yükleme.
- E-posta bildirimi.
- Hostinger üzerinde kurulum.
