-- Kurulu (canlı) veritabanına yeni görselleri ve videoları ekler.
-- Hiçbir kaydı silmez; birden fazla kez çalıştırılsa da aynı kayıtları tekrar eklemez.
-- Yalnızca hâlâ örnek görseli kullanan içeriklerin görselini değiştirir;
-- panelden sizin değiştirdiğiniz kayıtlara dokunmaz.

SET NAMES utf8mb4;

-- Tanıtım videosu (yalnızca boşsa ayarlanır)
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES ('home_video_url', '/assets/video/tanitim.mp4')
  ON DUPLICATE KEY UPDATE `setting_value` = IF(COALESCE(`setting_value`, '') = '', VALUES(`setting_value`), `setting_value`);
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES ('home_video_image', 'assets/img/etkinlik/sibyan-mektebi.jpg')
  ON DUPLICATE KEY UPDATE `setting_value` = IF(COALESCE(`setting_value`, '') = '', VALUES(`setting_value`), `setting_value`);
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES ('home_video_title', 'Müzemiz yeniden hayat buluyor')
  ON DUPLICATE KEY UPDATE `setting_value` = IF(COALESCE(`setting_value`, '') IN ('', 'Müzemizi tanıyın'), VALUES(`setting_value`), `setting_value`);

-- Ana sayfa açılış görseline arka plan videosu
UPDATE `hero_slides` SET `video` = '/assets/video/hero.mp4'
  WHERE `image` = 'assets/img/community.jpg' AND COALESCE(`video`, '') = '';

-- Örnek görseli kullanan içerikleri etkinlik fotoğraflarıyla güncelle
UPDATE `activities` SET `image` = 'assets/img/etkinlik/ogrenci-ziyareti.jpg'  WHERE `slug` = 'muze-ziyaretleri-rehberlik' AND `image` = 'assets/img/gallery-3.jpg';
UPDATE `activities` SET `image` = 'assets/img/etkinlik/karagoz-gosterisi.jpg' WHERE `slug` = 'egitim-atolyeleri' AND `image` = 'assets/img/gallery-4.jpg';
UPDATE `activities` SET `image` = 'assets/img/etkinlik/vitrin-gezisi.jpg'     WHERE `slug` = 'sergiler' AND `image` = 'assets/img/gallery-1.jpg';
UPDATE `projects`   SET `image` = 'assets/img/etkinlik/sibyan-mektebi.jpg'    WHERE `slug` = 'eski-sinif-canlaniyor' AND `image` = 'assets/img/education.jpg';
UPDATE `posts` SET `image` = 'assets/img/etkinlik/minik-ziyaretciler.jpg' WHERE `slug` = 'okul-gruplari-icin-yeni-donem-ziyaret-randevulari-acildi' AND `image` = 'assets/img/gallery-3.jpg';
UPDATE `posts` SET `image` = 'assets/img/etkinlik/cocuklar-gosteride.jpg' WHERE `slug` = 'eski-okul-oyunlari-atolyesi-takvimi-yayinda' AND `image` = 'assets/img/gallery-1.jpg';

-- Galerideki örnek başlıkları yeni obje fotoğraflarına uydur (yalnızca değiştirilmemişse)
UPDATE `gallery` SET `title` = 'Eski sınıfta öğretmen masası'          WHERE `image` = 'assets/img/community.jpg' AND `title` = 'Müze gezisi sonrası öğrenciler';
UPDATE `gallery` SET `title` = 'Kırmızı okul çantası'                  WHERE `image` = 'assets/img/gallery-3.jpg' AND `title` = 'Gönüllü rehberlerimiz';
UPDATE `gallery` SET `title` = 'Maarif Vekilliği ilkokul diploması'    WHERE `image` = 'assets/img/education.jpg' AND `title` = 'Güzel yazı atölyesi';
UPDATE `gallery` SET `title` = 'Okul bandosu trampeti'                 WHERE `image` = 'assets/img/nature.jpg'    AND `title` = 'Okul bahçesi etkinliği';
UPDATE `gallery` SET `title` = 'Hat ve güzel yazı takımı'              WHERE `image` = 'assets/img/gallery-1.jpg' AND `title` = 'Teneffüs oyunları günü';
UPDATE `gallery` SET `title` = 'Sayı boncuğu (abaküs)'                 WHERE `image` = 'assets/img/gallery-2.jpg' AND `title` = 'Gezici Müze Sandığı';
UPDATE `gallery` SET `title` = 'Dolma kalem ve mürekkep hokkası'       WHERE `image` = 'assets/img/gallery-4.jpg' AND `title` = 'Koleksiyon kayıt çalışması';
UPDATE `gallery` SET `title` = 'Resimli alfabe tablosu'                WHERE `image` = 'assets/img/gallery-5.jpg' AND `title` = 'Okul bostanı';
UPDATE `gallery` SET `title` = 'Öğretmenler Günü ilk gün zarfı'        WHERE `image` = 'assets/img/gallery-6.jpg' AND `title` = 'Sözlü tarih buluşması';
UPDATE `gallery` SET `title` = 'Toplu sınıf fotoğrafı'                 WHERE `image` = 'assets/img/together.jpg'  AND `title` = 'Kuşaklar bir arada';

-- Yeni etkinlik fotoğraflarını galerinin başına ekle (mevcut sıralamayı 16 kaydır)
UPDATE `gallery` SET `sort_order` = `sort_order` + 16
  WHERE `image` NOT LIKE 'assets/img/etkinlik/%'
    AND NOT EXISTS (SELECT 1 FROM (SELECT `id` FROM `gallery` WHERE `image` LIKE 'assets/img/etkinlik/%') AS g);

INSERT INTO `gallery` (`title`, `image`, `sort_order`, `is_published`)
SELECT t.title, t.image, t.sort_order, 1 FROM (
  SELECT 'Karagöz gösterisinde minik misafirler' AS title, 'assets/img/etkinlik/karagoz-gosterisi.jpg' AS image, 1 AS sort_order
  UNION ALL SELECT 'Eski sıralarda bir ders', 'assets/img/etkinlik/eski-siralarda.jpg', 2
  UNION ALL SELECT 'Sıbyan mektebi canlandırması', 'assets/img/etkinlik/sibyan-mektebi-canlandirma.jpg', 3
  UNION ALL SELECT 'Minik ziyaretçilerimiz', 'assets/img/etkinlik/minik-ziyaretciler.jpg', 4
  UNION ALL SELECT 'Merkezimizin önünde', 'assets/img/etkinlik/merkez-onunde.jpg', 5
  UNION ALL SELECT 'Açılış töreni', 'assets/img/etkinlik/acilis-toren.jpg', 6
  UNION ALL SELECT 'Eski sınıfta ziyaret', 'assets/img/etkinlik/eski-sinif-ziyaret.jpg', 7
  UNION ALL SELECT 'Vitrinler arasında', 'assets/img/etkinlik/vitrin-gezisi.jpg', 8
  UNION ALL SELECT 'Harita odası', 'assets/img/etkinlik/harita-odasi.jpg', 9
  UNION ALL SELECT 'Sıbyan mektebi bölümü', 'assets/img/etkinlik/sibyan-mektebi.jpg', 10
  UNION ALL SELECT 'Öğrenci grubu ziyareti', 'assets/img/etkinlik/ogrenci-ziyareti.jpg', 11
  UNION ALL SELECT 'Gösteriyi izleyen çocuklar', 'assets/img/etkinlik/cocuklar-gosteride.jpg', 12
  UNION ALL SELECT 'Belge vitrini', 'assets/img/etkinlik/belge-vitrini.jpg', 13
  UNION ALL SELECT 'Anı defteri', 'assets/img/etkinlik/ani-defteri.jpg', 14
  UNION ALL SELECT 'Karagöz ve Hacivat', 'assets/img/etkinlik/karagoz-hacivat.jpg', 15
  UNION ALL SELECT 'Gazi Okulu''nun eski hâli', 'assets/img/etkinlik/gazi-okulu-eski.jpg', 16
) AS t
WHERE NOT EXISTS (SELECT 1 FROM `gallery` g WHERE g.`image` = t.image);
