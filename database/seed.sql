-- İlkem Derneği (İlk Eğitim Müzesi Derneği) - örnek içerikler
-- Tüm metinler ve görseller örnektir; yönetim panelinden değiştirilebilir.
-- schema.sql dosyasından SONRA içe aktarın. Yönetici hesabı bu dosyada YOKTUR
-- (güvenlik için ilk yönetici kurulum adımıyla oluşturulur, bkz. README.md).

SET NAMES utf8mb4;

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'İlkem Derneği'),
('site_full_name', 'İlk Eğitim Müzesi Derneği'),
('site_tagline', 'İlk okul anılarını geleceğe taşıyoruz'),
('site_description', 'İlkem Derneği (İlk Eğitim Müzesi Derneği); ilköğretim tarihimizi, okul hatıralarını ve eğitim mirasını koruyan, sergileyen ve yeni kuşaklarla buluşturan bir sivil toplum kuruluşudur.'),
('logo', ''),
('logo_show_text', '1'),
('footer_text', 'İlk Eğitim Müzesi Derneği; okul sıralarından kara tahtalara, ilk okuma kitaplarından öğretmen hatıralarına uzanan eğitim mirasımızı yaşatmak için çalışır.'),
('hero_bottom_text', 'Her okul hatırası, ortak hafızamızın bir sayfası.'),
('contact_email', 'bilgi@ilkemdernegi.org'),
('contact_phone', '+90 (312) 000 00 00'),
('contact_address', 'Örnek Mahallesi, Eski Okul Sokağı No: 1\nÇankaya / Ankara'),
('contact_hours', 'Salı - Pazar 10:00 - 17:00\nPazartesi kapalı'),
('contact_intro', 'Müze ziyareti, okul grubu randevusu, bağış yapmak istediğiniz eski okul eşyaları veya gönüllülük için bize yazabilirsiniz.'),
('contact_map_embed', ''),
('social_facebook', 'https://www.facebook.com/'),
('social_instagram', 'https://www.instagram.com/'),
('social_twitter', 'https://x.com/'),
('social_youtube', 'https://www.youtube.com/'),
('social_linkedin', ''),
('social_whatsapp', ''),
('about_title', 'Hakkımızda'),
('about_lead', 'İlköğretim tarihimizi koruyan, sergileyen ve yeni kuşaklarla buluşturan bir dernek.'),
('about_heading', 'Okul hatıraları, ortak hafızamız.'),
('about_note', 'Bir kara tahta, bir kuşağın hikâyesidir.'),
('about_image', 'assets/img/together.jpg'),
('about_banner_image', ''),
('about_summary', 'İlkem, İlk Eğitim Müzesi Derneği’nin kısaltmasıdır. Okul sıralarından ilk okuma kitaplarına, karne ve fotoğraflardan öğretmen hatıralarına kadar ilköğretim mirasımızı topluyor, koruyor ve sergiliyoruz.\n\nAmacımız, geçmişin sınıflarını bugünün çocuklarıyla buluşturan, yaşayan bir eğitim müzesi oluşturmak.'),
('about_content', 'İlkem Derneği (İlk Eğitim Müzesi Derneği), ilköğretimin tarihine ve okul kültürüne ait eşya, belge ve hatıraları gelecek kuşaklara aktarmak amacıyla gönüllüler tarafından kurulmuştur. (Bu metin örnektir.)\n\nOkul sıraları, kara tahtalar, mürekkep hokkaları, ders kitapları, karneler, sınıf fotoğrafları ve öğretmen defterleri; her biri bir dönemin eğitim anlayışını ve gündelik okul hayatını anlatır. Biz bu mirası bir araya getiriyor, belgeliyor ve herkesin ziyaret edebileceği bir müze ortamında sergiliyoruz.\n\n## Neler yapıyoruz?\n\n- Eski okul eşyalarını ve belgelerini bağış yoluyla topluyor, envanterini çıkarıyoruz.\n- Öğretmen ve öğrenci hatıralarını sözlü tarih çalışmalarıyla kayda alıyoruz.\n- Okul gruplarına müze gezileri ve eğitim atölyeleri düzenliyoruz.\n- Dönemsel sergiler ve söyleşilerle eğitim tarihini görünür kılıyoruz.\n\nEvinizde saklanan bir karne, bir okuma kitabı ya da bir sınıf fotoğrafı da bu hafızanın parçası olabilir.'),
('about_mission', 'İlköğretim tarihimize ait eşya, belge ve hatıraları toplamak, bilimsel yöntemlerle korumak ve eğitici sergiler aracılığıyla toplumun her kesimiyle buluşturmak.'),
('about_vision', 'Geçmişin sınıflarını bugünün öğrencileriyle buluşturan, eğitim mirasımızın yaşayan hafızası olan erişilebilir bir müze olmak.'),
('about_values', 'Eğitim mirasına saygı\nToplumsal hafıza\nErişilebilirlik\nGönüllülük\nŞeffaflık\nKuşaklar arası paylaşım'),
('stat1_value', '1.200+'),
('stat1_label', 'Koleksiyon parçası'),
('stat2_value', '85'),
('stat2_label', 'Okul ziyareti'),
('stat3_value', '150+'),
('stat3_label', 'Sözlü tarih kaydı'),
('activities_lead', 'Koleksiyondan sergilere, okul gezilerinden sözlü tarihe; eğitim mirasımızı yaşatan çalışmalarımızı keşfedin.'),
('projects_lead', 'İlköğretim tarihimizi korumak ve paylaşmak için yürüttüğümüz projeler.'),
('home_activities_title', 'Eğitim mirasını yaşatan çalışmalar.'),
('home_projects_title', 'Geçmişin sınıfları, geleceğin hafızası.'),
('home_news_title', 'Son haberler.'),
('home_media_title', 'Müzeden kareler.'),
('home_video_url', '/assets/video/tanitim.mp4'),
('home_video_title', 'Müzemiz yeniden hayat buluyor'),
('home_video_image', 'assets/img/etkinlik/sibyan-mektebi.jpg'),
('volunteer_eyebrow', 'Hafızamızı birlikte koruyalım'),
('volunteer_title', 'Bir hatıra da sizden olsun.'),
('volunteer_text', 'Gönüllü olabilir, eski okul eşyalarınızı ve hatıralarınızı müzemize bağışlayabilirsiniz.'),
('volunteer_button_text', 'Gönüllü Olmak İstiyorum'),
('volunteer_button_url', '/iletisim?konu=gonullu')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

INSERT INTO `hero_slides` (`kicker`, `title`, `subtitle`, `image`, `video`, `button1_text`, `button1_url`, `button2_text`, `button2_url`, `sort_order`, `is_published`) VALUES
('İlk Eğitim Müzesi Derneği', 'İlk okul anılarını geleceğe taşıyoruz.', 'Kara tahtalardan ilk okuma kitaplarına, eğitim mirasımızı\nkoruyor, sergiliyor ve yeni kuşaklarla buluşturuyoruz.', 'assets/img/community.jpg', '/assets/video/hero.mp4', 'Derneğimizi Tanıyın', '/hakkimizda', 'Neler yapıyoruz?', '/faaliyetlerimiz', 1, 1),
('Bağış ve gönüllülük', 'Evinizdeki bir karne de müzenin parçası olabilir.', 'Eski okul eşyalarınızı, fotoğraflarınızı ve hatıralarınızı müzemize bağışlayarak ortak hafızamıza katkı sunun.', 'assets/img/gallery-3.jpg', '', 'Bağış Yapmak İstiyorum', '/iletisim?konu=bagis', 'Projelerimiz', '/projeler', 2, 1);

INSERT INTO `activities` (`title`, `slug`, `icon`, `summary`, `content`, `image`, `sort_order`, `is_published`) VALUES
('Müze Ziyaretleri & Rehberlik', 'muze-ziyaretleri-rehberlik', 'home', 'Okul grupları ve bireysel ziyaretçiler için rehberli müze gezileri ve canlandırılmış eski sınıf deneyimi.', 'Müzemizde, farklı dönemlere ait sınıf düzenlerini yeniden canlandırıyoruz. Ziyaretçilerimiz eski okul sıralarına oturabilir, kara tahtaya tebeşirle yazabilir, mürekkepli kalemle yazı deneyebilir.\n\n## Ziyaret seçenekleri\n\n- Okul grupları için rehberli geziler (randevulu)\n- Aileler için hafta sonu gezileri\n- Öğretmen ve eğitim fakültesi öğrencileri için tematik turlar\n\nGrup randevusu için iletişim formunu kullanabilirsiniz. (Örnek içerik)', 'assets/img/etkinlik/ogrenci-ziyareti.jpg', 1, 1),
('Koleksiyon & Arşiv', 'koleksiyon-arsiv', 'book', 'Okul eşyaları, ders kitapları, karneler ve fotoğraflardan oluşan koleksiyonumuzu topluyor ve belgeliyoruz.', 'Koleksiyonumuz bağışlarla büyüyor. Her parça kayıt altına alınıyor, fotoğraflanıyor ve uygun koşullarda korunuyor.\n\n## Koleksiyonumuzda neler var?\n\n- Ders kitapları ve ilk okuma kitapları\n- Karneler, diplomalar ve okul belgeleri\n- Okul sıraları, kara tahtalar ve sınıf gereçleri\n- Sınıf ve okul fotoğrafları\n\nBağış yapmak isterseniz iletişim formundan "Destek ve bağış" konusunu seçebilirsiniz. (Örnek içerik)', 'assets/img/education.jpg', 2, 1),
('Eğitim Atölyeleri', 'egitim-atolyeleri', 'lightbulb', 'Çocuklar için güzel yazı, eski oyunlar ve okul kültürü üzerine eğlenceli atölyeler.', 'Atölyelerimizde çocuklar geçmişin okul hayatını oynayarak ve deneyerek öğreniyor.\n\n- Hokka ve divitle güzel yazı atölyesi\n- Teneffüs oyunları atölyesi\n- "Dedemin karnesi" aile atölyesi\n\nTüm atölyeler gönüllü eğitmenlerimiz tarafından yürütülür. (Örnek içerik)', 'assets/img/etkinlik/karagoz-gosterisi.jpg', 3, 1),
('Sergiler', 'sergiler', 'palette', 'Eğitim tarihinin farklı dönemlerini ve temalarını konu alan kalıcı ve dönemsel sergiler.', 'Kalıcı sergimizin yanı sıra her yıl farklı temalarda dönemsel sergiler hazırlıyoruz: köy okulları, ders kitaplarının değişimi, okul formaları ve öğretmen portreleri bunlardan bazıları.\n\nSergilerimizin bir bölümü okullara ve kültür merkezlerine gezici olarak da taşınabilmektedir. (Örnek içerik)', 'assets/img/etkinlik/vitrin-gezisi.jpg', 4, 1),
('Sözlü Tarih', 'sozlu-tarih', 'users', 'Emekli öğretmenlerimizin ve eski öğrencilerin okul hatıralarını kayda alıyoruz.', 'Eğitim tarihimiz yalnızca belgelerde değil, onu yaşayanların hafızasında da saklı. Sözlü tarih çalışmalarımızla emekli öğretmenlerimizin ve farklı kuşaklardan öğrencilerin anlatımlarını kayıt altına alıyoruz.\n\nKayıtlar, izin alınarak müze arşivine eklenir ve sergilerde kullanılır. (Örnek içerik)', 'assets/img/together.jpg', 5, 1),
('Gönüllülük', 'gonulluluk', 'hand', 'Rehberlikten arşiv çalışmasına, müzemizde gönüllü olarak görev alabilirsiniz.', 'Gönüllülerimiz müzemizin kalbidir. Ziyaretçi karşılama, rehberlik, koleksiyon kayıt çalışması, atölyeler ve sözlü tarih görüşmeleri gibi farklı alanlarda destek olabilirsiniz.\n\nBaşvuru için iletişim formundan "Gönüllülük başvurusu" konusunu seçmeniz yeterli. (Örnek içerik)', 'assets/img/gallery-6.jpg', 6, 1);

INSERT INTO `projects` (`title`, `slug`, `category`, `status`, `location`, `start_date`, `end_date`, `summary`, `content`, `image`, `is_featured`, `sort_order`, `is_published`) VALUES
('Eski Sınıf Canlanıyor', 'eski-sinif-canlaniyor', 'Müze & Sergi', 'devam', 'Ankara', '2026-02-01', NULL, 'Farklı dönemlere ait sınıf düzenlerini müzede birebir yeniden kuruyoruz.', 'Eski Sınıf Canlanıyor projesiyle 1930’lardan 1980’lere uzanan dört farklı sınıf düzenini müzemizde yeniden kuruyoruz.\n\n## Projenin hedefleri\n\n- Dört dönem sınıfının özgün eşyalarla kurulması\n- Her yıl en az 100 okul grubunun ağırlanması\n- Ziyaretçilere dokunarak öğrenme deneyimi sunulması\n\n(Örnek içerik)', 'assets/img/etkinlik/sibyan-mektebi.jpg', 1, 1, 1),
('Okul Hatıraları Arşivi', 'okul-hatiralari-arsivi', 'Sözlü Tarih & Arşiv', 'devam', 'Ankara ve çevre iller', '2026-04-15', NULL, 'Emekli öğretmenlerin ve eski öğrencilerin anlatımlarını ve belgelerini dijital arşive kazandırıyoruz.', 'Okul Hatıraları Arşivi projesinde gönüllülerimiz, emekli öğretmenlerimizle ve farklı kuşaklardan eski öğrencilerle görüşmeler yapıyor; fotoğraf ve belgeleri tarayarak dijital arşive ekliyor.\n\nProje süresince 300 görüşme yapılması ve 5.000 belgenin dijitalleştirilmesi hedeflenmektedir. (Örnek içerik)', 'assets/img/together.jpg', 1, 2, 1),
('Gezici Müze Sandığı', 'gezici-muze-sandigi', 'Eğitim', 'tamamlandi', 'Köy ve kasaba okulları', '2025-09-01', '2026-03-31', 'Müzeye gelemeyen öğrenciler için eski okul eşyalarıyla dolu bir sandık okul okul dolaştı.', 'Gezici Müze Sandığı ile müzemize ulaşamayan okullara gittik. Sandıktaki eski ders kitapları, hokkalar, karneler ve fotoğraflarla öğrenciler geçmişin okul hayatını yakından tanıdı.\n\nProje süresince 40 okulda 3.000’den fazla öğrenciye ulaşıldı. (Örnek içerik)', 'assets/img/gallery-1.jpg', 1, 3, 1),
('Dijital Müze', 'dijital-muze', 'Dijital', 'planlaniyor', 'Çevrim içi', '2027-01-10', NULL, 'Koleksiyonumuzu herkesin her yerden gezebileceği çevrim içi bir müzeye dönüştürüyoruz.', 'Dijital Müze projesiyle koleksiyonumuzdaki parçaları yüksek çözünürlüklü fotoğraflar ve açıklamalarla çevrim içi olarak erişime açmayı planlıyoruz.\n\nProje ayrıntıları yakında duyurulacaktır. (Örnek içerik)', 'assets/img/gallery-2.jpg', 0, 4, 1);

INSERT INTO `posts` (`type`, `title`, `slug`, `summary`, `content`, `image`, `published_at`, `is_published`) VALUES
('duyuru', 'Okul grupları için yeni dönem ziyaret randevuları açıldı', 'okul-gruplari-icin-yeni-donem-ziyaret-randevulari-acildi', 'Rehberli müze gezileri ve atölyeler için okul grubu başvurularımız başladı.', 'Yeni eğitim-öğretim dönemi için okul grubu ziyaret randevularımız açıldı. Rehberli müze gezisi ve isteğe bağlı atölye programı ile öğrencilerinizi geçmişin sınıflarına davet ediyoruz.\n\n## Nasıl randevu alabilirim?\n\n- İletişim sayfamızdaki formu doldurun.\n- Okulunuzun adını, sınıf düzeyini ve öğrenci sayısını yazın.\n- Tercih ettiğiniz tarih aralığını belirtin.\n\nEkibimiz en kısa sürede sizinle iletişime geçecek. (Örnek duyuru)', 'assets/img/etkinlik/minik-ziyaretciler.jpg', NOW() - INTERVAL 1 DAY, 1),
('haber', 'Bağış kampanyasında yüzüncü karne müzemize ulaştı', 'bagis-kampanyasinda-yuzuncu-karne-muzemize-ulasti', '"Karneni Getir" kampanyamızla toplanan karne sayısı yüzü geçti.', '"Karneni Getir" kampanyamıza gösterilen ilgi bizi çok sevindirdi. Farklı dönemlere ait yüzden fazla karne, ailelerin hikâyeleriyle birlikte müzemize bağışlandı.\n\nBağışlanan karneler, kayıt ve koruma çalışmalarının ardından dönemsel sergimizde yer alacak. (Örnek haber)', 'assets/img/education.jpg', NOW() - INTERVAL 4 DAY, 1),
('duyuru', '"Eski Okul Oyunları" atölyesi takvimi yayında', 'eski-okul-oyunlari-atolyesi-takvimi-yayinda', 'Çocuklar için teneffüs oyunları atölyemizin bu ayki tarihleri belli oldu.', 'Teneffüs zillerinin çaldığı, seksek ve yakan topun oynandığı günlere dönüyoruz. Bu ay düzenlenecek atölyelerin takvimi hazırlandı.\n\n- 7-10 yaş grubu atölyesi\n- Aile atölyesi\n- Öğretmenler için oyun ve eğitim buluşması\n\nKatılım ücretsizdir; kontenjan sınırlıdır. (Örnek duyuru)', 'assets/img/etkinlik/cocuklar-gosteride.jpg', NOW() - INTERVAL 7 DAY, 1),
('haber', 'Emekli öğretmenlerimizle sözlü tarih buluşması', 'emekli-ogretmenlerimizle-sozlu-tarih-bulusmasi', 'Okul Hatıraları Arşivi projesi kapsamında emekli öğretmenlerimiz anılarını paylaştı.', 'Okul Hatıraları Arşivi projesi kapsamında düzenlediğimiz buluşmada emekli öğretmenlerimiz, köy okullarında geçen ilk görev yıllarını ve unutamadıkları öğrencilerini anlattı.\n\nKayda alınan anlatımlar müze arşivimize eklenecek. (Örnek haber)', 'assets/img/together.jpg', NOW() - INTERVAL 12 DAY, 1),
('haber', 'Gezici Müze Sandığı projesi tamamlandı', 'gezici-muze-sandigi-projesi-tamamlandi', 'Altı ay süren projede 40 okulda 3.000’den fazla öğrenciye ulaşıldı.', 'Gezici Müze Sandığı projemizi başarıyla tamamladık. Sandığımız altı ay boyunca köy ve kasaba okullarını dolaştı.\n\nEmeği geçen tüm gönüllülerimize ve bizi ağırlayan okullarımıza teşekkür ederiz. (Örnek haber)', 'assets/img/gallery-2.jpg', NOW() - INTERVAL 20 DAY, 1),
('duyuru', 'Genel kurul toplantısı duyurusu', 'genel-kurul-toplantisi-duyurusu', 'Derneğimizin olağan genel kurul toplantısına tüm üyelerimiz davetlidir.', 'İlk Eğitim Müzesi Derneği olağan genel kurul toplantısı dernek merkezimizde yapılacaktır. Tüm üyelerimizin katılımını rica ederiz.\n\nGündem maddeleri toplantıdan önce üyelerimizle paylaşılacaktır. (Örnek duyuru)', '', NOW() - INTERVAL 30 DAY, 1);

INSERT INTO `gallery` (`title`, `image`, `sort_order`, `is_published`) VALUES
('Karagöz gösterisinde minik misafirler', 'assets/img/etkinlik/karagoz-gosterisi.jpg', 1, 1),
('Eski sıralarda bir ders', 'assets/img/etkinlik/eski-siralarda.jpg', 2, 1),
('Sıbyan mektebi canlandırması', 'assets/img/etkinlik/sibyan-mektebi-canlandirma.jpg', 3, 1),
('Minik ziyaretçilerimiz', 'assets/img/etkinlik/minik-ziyaretciler.jpg', 4, 1),
('Merkezimizin önünde', 'assets/img/etkinlik/merkez-onunde.jpg', 5, 1),
('Açılış töreni', 'assets/img/etkinlik/acilis-toren.jpg', 6, 1),
('Eski sınıfta ziyaret', 'assets/img/etkinlik/eski-sinif-ziyaret.jpg', 7, 1),
('Vitrinler arasında', 'assets/img/etkinlik/vitrin-gezisi.jpg', 8, 1),
('Harita odası', 'assets/img/etkinlik/harita-odasi.jpg', 9, 1),
('Sıbyan mektebi bölümü', 'assets/img/etkinlik/sibyan-mektebi.jpg', 10, 1),
('Öğrenci grubu ziyareti', 'assets/img/etkinlik/ogrenci-ziyareti.jpg', 11, 1),
('Gösteriyi izleyen çocuklar', 'assets/img/etkinlik/cocuklar-gosteride.jpg', 12, 1),
('Belge vitrini', 'assets/img/etkinlik/belge-vitrini.jpg', 13, 1),
('Anı defteri', 'assets/img/etkinlik/ani-defteri.jpg', 14, 1),
('Karagöz ve Hacivat', 'assets/img/etkinlik/karagoz-hacivat.jpg', 15, 1),
('Gazi Okulu''nun eski hâli', 'assets/img/etkinlik/gazi-okulu-eski.jpg', 16, 1),
('Eski sınıfta öğretmen masası', 'assets/img/community.jpg', 17, 1),
('Kırmızı okul çantası', 'assets/img/gallery-3.jpg', 18, 1),
('Maarif Vekilliği ilkokul diploması', 'assets/img/education.jpg', 19, 1),
('Okul bandosu trampeti', 'assets/img/nature.jpg', 20, 1),
('Hat ve güzel yazı takımı', 'assets/img/gallery-1.jpg', 21, 1),
('Sayı boncuğu (abaküs)', 'assets/img/gallery-2.jpg', 22, 1),
('Dolma kalem ve mürekkep hokkası', 'assets/img/gallery-4.jpg', 23, 1),
('Resimli alfabe tablosu', 'assets/img/gallery-5.jpg', 24, 1),
('Öğretmenler Günü ilk gün zarfı', 'assets/img/gallery-6.jpg', 25, 1),
('Toplu sınıf fotoğrafı', 'assets/img/together.jpg', 26, 1);
