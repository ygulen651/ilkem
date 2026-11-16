<?php
declare(strict_types=1);

if (!defined('ROOT_PATH')) {
    http_response_code(403);
    exit;
}

/**
 * Yönetim panelindeki içerik türlerinin tanımları.
 * Ekleme/düzenleme/silme/yayın durumu işlemleri bu tanımlardan otomatik üretilir.
 * Alan türleri: text, textarea, richtext, image, select, date, datetime, number, checkbox, link
 */

const LINK_HELP = 'Site içi sayfa için /hakkimizda gibi, dış bağlantı için https:// ile başlayan adres yazın.';
const RICHTEXT_HELP = 'Boş satır yeni paragraf başlatır. "## " ile başlayan satır ara başlık, "- " ile başlayan satırlar madde listesi olur. **kalın** yazabilirsiniz.';

function admin_resources(): array
{
    return [
        'slides' => [
            'table' => 'hero_slides', 'title' => 'Ana Sayfa Görselleri', 'singular' => 'Görsel', 'icon' => 'image',
            'description' => 'Ana sayfanın en üstündeki geniş görselli açılış alanı. Birden fazla kayıt eklerseniz slayt olarak döner.',
            'order' => 'sort_order ASC, id ASC', 'search' => ['title', 'kicker'],
            'columns' => ['image' => 'Görsel', 'title' => 'Başlık', 'sort_order' => 'Sıra'],
            'fields' => [
                ['name' => 'kicker', 'label' => 'Üst başlık', 'type' => 'text', 'max' => 150],
                ['name' => 'title', 'label' => 'Başlık', 'type' => 'text', 'required' => true, 'max' => 200],
                ['name' => 'subtitle', 'label' => 'Açıklama', 'type' => 'textarea', 'max' => 500, 'rows' => 3],
                ['name' => 'image', 'label' => 'Arka plan görseli', 'type' => 'image', 'help' => 'Önerilen boyut: en az 1920×1080 piksel, yatay.'],
                ['name' => 'video', 'label' => 'Arka plan videosu (isteğe bağlı)', 'type' => 'link', 'max' => 255, 'help' => 'MP4/WEBM dosya adresi, ör. /uploads/video/tanitim.mp4. Doluysa görsel, video yüklenirken kapak olarak kullanılır.'],
                ['name' => 'button1_text', 'label' => '1. buton yazısı', 'type' => 'text', 'max' => 80, 'half' => true],
                ['name' => 'button1_url', 'label' => '1. buton bağlantısı', 'type' => 'link', 'max' => 255, 'half' => true, 'help' => LINK_HELP],
                ['name' => 'button2_text', 'label' => '2. buton yazısı', 'type' => 'text', 'max' => 80, 'half' => true],
                ['name' => 'button2_url', 'label' => '2. buton bağlantısı', 'type' => 'link', 'max' => 255, 'half' => true],
                ['name' => 'sort_order', 'label' => 'Sıra', 'type' => 'number', 'default' => 0, 'half' => true],
                ['name' => 'is_published', 'label' => 'Yayında', 'type' => 'checkbox', 'default' => 1],
            ],
        ],
        'activities' => [
            'table' => 'activities', 'title' => 'Faaliyetler', 'singular' => 'Faaliyet', 'icon' => 'sprout', 'slug' => true,
            'public' => 'faaliyetlerimiz/',
            'order' => 'sort_order ASC, id ASC', 'search' => ['title', 'summary'],
            'columns' => ['image' => 'Görsel', 'title' => 'Başlık', 'sort_order' => 'Sıra'],
            'fields' => [
                ['name' => 'title', 'label' => 'Başlık', 'type' => 'text', 'required' => true, 'max' => 200],
                ['name' => 'slug', 'label' => 'Bağlantı adı (URL)', 'type' => 'text', 'max' => 190, 'help' => 'Boş bırakırsanız başlıktan otomatik oluşturulur.'],
                ['name' => 'icon', 'label' => 'Simge', 'type' => 'select', 'options' => ACTIVITY_ICONS, 'default' => 'star', 'half' => true],
                ['name' => 'sort_order', 'label' => 'Sıra', 'type' => 'number', 'default' => 0, 'half' => true],
                ['name' => 'summary', 'label' => 'Kısa açıklama', 'type' => 'textarea', 'max' => 500, 'rows' => 3],
                ['name' => 'content', 'label' => 'Detay içeriği', 'type' => 'richtext', 'help' => RICHTEXT_HELP],
                ['name' => 'image', 'label' => 'Görsel', 'type' => 'image'],
                ['name' => 'is_published', 'label' => 'Yayında', 'type' => 'checkbox', 'default' => 1],
            ],
        ],
        'projects' => [
            'table' => 'projects', 'title' => 'Projeler', 'singular' => 'Proje', 'icon' => 'lightbulb', 'slug' => true,
            'public' => 'projeler/',
            'order' => 'sort_order ASC, id DESC', 'search' => ['title', 'summary', 'category'],
            'columns' => ['image' => 'Görsel', 'title' => 'Başlık', 'status' => 'Durum', 'is_featured' => 'Öne çıkan'],
            'filter' => ['field' => 'status', 'options' => PROJECT_STATUSES],
            'fields' => [
                ['name' => 'title', 'label' => 'Başlık', 'type' => 'text', 'required' => true, 'max' => 200],
                ['name' => 'slug', 'label' => 'Bağlantı adı (URL)', 'type' => 'text', 'max' => 190, 'help' => 'Boş bırakırsanız başlıktan otomatik oluşturulur.'],
                ['name' => 'category', 'label' => 'Kategori / alan', 'type' => 'text', 'max' => 100, 'half' => true],
                ['name' => 'status', 'label' => 'Durum', 'type' => 'select', 'options' => PROJECT_STATUSES, 'default' => 'devam', 'half' => true],
                ['name' => 'location', 'label' => 'Yer', 'type' => 'text', 'max' => 150, 'half' => true],
                ['name' => 'sort_order', 'label' => 'Sıra', 'type' => 'number', 'default' => 0, 'half' => true],
                ['name' => 'start_date', 'label' => 'Başlangıç tarihi', 'type' => 'date', 'half' => true],
                ['name' => 'end_date', 'label' => 'Bitiş tarihi', 'type' => 'date', 'half' => true],
                ['name' => 'summary', 'label' => 'Kısa açıklama', 'type' => 'textarea', 'max' => 500, 'rows' => 3],
                ['name' => 'content', 'label' => 'Detay içeriği', 'type' => 'richtext', 'help' => RICHTEXT_HELP],
                ['name' => 'image', 'label' => 'Kapak görseli', 'type' => 'image'],
                ['name' => 'is_featured', 'label' => 'Ana sayfada öne çıkar', 'type' => 'checkbox', 'default' => 0],
                ['name' => 'is_published', 'label' => 'Yayında', 'type' => 'checkbox', 'default' => 1],
            ],
        ],
        'posts' => [
            'table' => 'posts', 'title' => 'Haberler & Duyurular', 'singular' => 'Haber / Duyuru', 'icon' => 'megaphone', 'slug' => true,
            'public' => 'haberler/',
            'order' => 'published_at DESC, id DESC', 'search' => ['title', 'summary'],
            'columns' => ['image' => 'Görsel', 'title' => 'Başlık', 'type' => 'Tür', 'published_at' => 'Tarih'],
            'filter' => ['field' => 'type', 'options' => POST_TYPES],
            'fields' => [
                ['name' => 'type', 'label' => 'Tür', 'type' => 'select', 'options' => POST_TYPES, 'default' => 'haber', 'half' => true],
                ['name' => 'published_at', 'label' => 'Yayın tarihi', 'type' => 'datetime', 'default' => 'now', 'half' => true, 'help' => 'İleri bir tarih seçerseniz içerik o tarihte görünür.'],
                ['name' => 'title', 'label' => 'Başlık', 'type' => 'text', 'required' => true, 'max' => 200],
                ['name' => 'slug', 'label' => 'Bağlantı adı (URL)', 'type' => 'text', 'max' => 190, 'help' => 'Boş bırakırsanız başlıktan otomatik oluşturulur.'],
                ['name' => 'summary', 'label' => 'Özet', 'type' => 'textarea', 'max' => 500, 'rows' => 3],
                ['name' => 'content', 'label' => 'İçerik', 'type' => 'richtext', 'help' => RICHTEXT_HELP],
                ['name' => 'image', 'label' => 'Görsel', 'type' => 'image'],
                ['name' => 'is_published', 'label' => 'Yayında', 'type' => 'checkbox', 'default' => 1],
            ],
        ],
        'gallery' => [
            'table' => 'gallery', 'title' => 'Fotoğraf Galerisi', 'singular' => 'Fotoğraf', 'icon' => 'image',
            'order' => 'sort_order ASC, id DESC', 'search' => ['title'], 'bulk_upload' => true,
            'columns' => ['image' => 'Görsel', 'title' => 'Başlık', 'sort_order' => 'Sıra'],
            'fields' => [
                ['name' => 'title', 'label' => 'Başlık / açıklama', 'type' => 'text', 'max' => 200],
                ['name' => 'image', 'label' => 'Fotoğraf', 'type' => 'image', 'required' => true],
                ['name' => 'sort_order', 'label' => 'Sıra', 'type' => 'number', 'default' => 0, 'half' => true],
                ['name' => 'is_published', 'label' => 'Yayında', 'type' => 'checkbox', 'default' => 1],
            ],
        ],
    ];
}

/** Site ayarları grupları. */
function admin_settings_groups(): array
{
    return [
        'general' => [
            'title' => 'Genel Ayarlar', 'icon' => 'star',
            'fields' => [
                ['name' => 'site_name', 'label' => 'Site adı', 'type' => 'text', 'max' => 100, 'required' => true, 'help' => 'Kısa ad. Üst menüde ilk kelime büyük yazılır (ör. İlkem Derneği).'],
                ['name' => 'site_full_name', 'label' => 'Kurumun açık adı', 'type' => 'text', 'max' => 150, 'help' => 'Logonun altında küçük satırda ve alt bilgide görünür. Ör. İlk Eğitim Müzesi Derneği'],
                ['name' => 'site_tagline', 'label' => 'Slogan', 'type' => 'text', 'max' => 150],
                ['name' => 'site_description', 'label' => 'Site açıklaması (arama motorları için)', 'type' => 'textarea', 'max' => 300, 'rows' => 2],
                ['name' => 'logo', 'label' => 'Logo', 'type' => 'image', 'help' => 'PNG/WEBP önerilir, şeffaf arka plan, yükseklik en az 120 px. Boşsa geçici logo kullanılır.'],
                ['name' => 'logo_show_text', 'label' => 'Logonun yanında site adını göster', 'type' => 'checkbox', 'default' => 1],
                ['name' => 'footer_text', 'label' => 'Alt bilgi metni', 'type' => 'textarea', 'max' => 400, 'rows' => 2],
            ],
        ],
        'home' => [
            'title' => 'Ana Sayfa Bölümleri', 'icon' => 'home',
            'fields' => [
                ['name' => 'hero_bottom_text', 'label' => 'Açılış alanı alt yazısı', 'type' => 'text', 'max' => 150],
                ['name' => 'home_activities_title', 'label' => 'Faaliyetler bölümü başlığı', 'type' => 'text', 'max' => 150],
                ['name' => 'home_projects_title', 'label' => 'Projeler bölümü başlığı', 'type' => 'text', 'max' => 150],
                ['name' => 'home_news_title', 'label' => 'Haberler bölümü başlığı', 'type' => 'text', 'max' => 150],
                ['name' => 'home_media_title', 'label' => 'Fotoğraf/video bölümü başlığı', 'type' => 'text', 'max' => 150],
                ['name' => 'home_video_url', 'label' => 'Tanıtım videosu (YouTube bağlantısı)', 'type' => 'link', 'max' => 255, 'help' => 'Ör. https://www.youtube.com/watch?v=XXXXXXXXXXX — boşsa yalnızca fotoğraflar gösterilir.'],
                ['name' => 'home_video_title', 'label' => 'Video başlığı', 'type' => 'text', 'max' => 150],
                ['name' => 'home_video_image', 'label' => 'Video kapak görseli', 'type' => 'image', 'help' => 'Boşsa YouTube kapak görseli kullanılır.'],
                ['name' => 'volunteer_eyebrow', 'label' => 'Gönüllü çağrısı - üst yazı', 'type' => 'text', 'max' => 100],
                ['name' => 'volunteer_title', 'label' => 'Gönüllü çağrısı - başlık', 'type' => 'text', 'max' => 150],
                ['name' => 'volunteer_text', 'label' => 'Gönüllü çağrısı - metin', 'type' => 'textarea', 'max' => 400, 'rows' => 2],
                ['name' => 'volunteer_button_text', 'label' => 'Gönüllü çağrısı - buton yazısı', 'type' => 'text', 'max' => 80],
                ['name' => 'volunteer_button_url', 'label' => 'Gönüllü çağrısı - buton bağlantısı', 'type' => 'link', 'max' => 255, 'help' => LINK_HELP . ' Üst menüdeki "Gönüllü Ol" butonu da bu adrese gider.'],
                ['name' => 'activities_lead', 'label' => 'Faaliyetler sayfası giriş metni', 'type' => 'textarea', 'max' => 300, 'rows' => 2],
                ['name' => 'projects_lead', 'label' => 'Projeler sayfası giriş metni', 'type' => 'textarea', 'max' => 300, 'rows' => 2],
            ],
        ],
        'about' => [
            'title' => 'Hakkımızda', 'icon' => 'users',
            'fields' => [
                ['name' => 'about_title', 'label' => 'Sayfa başlığı', 'type' => 'text', 'max' => 100],
                ['name' => 'about_lead', 'label' => 'Sayfa giriş cümlesi', 'type' => 'text', 'max' => 250],
                ['name' => 'about_heading', 'label' => 'Bölüm başlığı', 'type' => 'text', 'max' => 150],
                ['name' => 'about_summary', 'label' => 'Ana sayfadaki kısa tanıtım', 'type' => 'richtext', 'rows' => 5, 'help' => 'Ana sayfadaki "Bizi tanıyın" bölümünde görünür.'],
                ['name' => 'about_content', 'label' => 'Hakkımızda içeriği', 'type' => 'richtext', 'help' => RICHTEXT_HELP],
                ['name' => 'about_mission', 'label' => 'Misyonumuz', 'type' => 'textarea', 'rows' => 3],
                ['name' => 'about_vision', 'label' => 'Vizyonumuz', 'type' => 'textarea', 'rows' => 3],
                ['name' => 'about_values', 'label' => 'Değerlerimiz', 'type' => 'textarea', 'rows' => 5, 'help' => 'Her satıra bir değer yazın.'],
                ['name' => 'about_image', 'label' => 'Tanıtım görseli', 'type' => 'image'],
                ['name' => 'about_note', 'label' => 'Görsel üzerindeki not', 'type' => 'text', 'max' => 100],
                ['name' => 'about_banner_image', 'label' => 'Sayfa başlığı arka plan görseli (isteğe bağlı)', 'type' => 'image'],
                ['name' => 'stat1_value', 'label' => '1. sayı', 'type' => 'text', 'max' => 20, 'half' => true],
                ['name' => 'stat1_label', 'label' => '1. sayı açıklaması', 'type' => 'text', 'max' => 50, 'half' => true],
                ['name' => 'stat2_value', 'label' => '2. sayı', 'type' => 'text', 'max' => 20, 'half' => true],
                ['name' => 'stat2_label', 'label' => '2. sayı açıklaması', 'type' => 'text', 'max' => 50, 'half' => true],
                ['name' => 'stat3_value', 'label' => '3. sayı', 'type' => 'text', 'max' => 20, 'half' => true],
                ['name' => 'stat3_label', 'label' => '3. sayı açıklaması', 'type' => 'text', 'max' => 50, 'half' => true],
            ],
        ],
        'contact' => [
            'title' => 'İletişim Bilgileri', 'icon' => 'phone',
            'fields' => [
                ['name' => 'contact_email', 'label' => 'E-posta', 'type' => 'email', 'max' => 150, 'half' => true],
                ['name' => 'contact_phone', 'label' => 'Telefon', 'type' => 'text', 'max' => 40, 'half' => true],
                ['name' => 'contact_address', 'label' => 'Adres', 'type' => 'textarea', 'max' => 400, 'rows' => 3],
                ['name' => 'contact_hours', 'label' => 'Çalışma saatleri', 'type' => 'textarea', 'max' => 300, 'rows' => 2],
                ['name' => 'contact_intro', 'label' => 'İletişim sayfası açıklaması', 'type' => 'textarea', 'max' => 400, 'rows' => 2],
                ['name' => 'contact_map_embed', 'label' => 'Google Haritalar gömme adresi', 'type' => 'link', 'max' => 1000, 'help' => 'Google Haritalar > Paylaş > Harita yerleştir bölümündeki iframe kodunda yer alan src="https://www.google.com/maps/embed?..." adresini yapıştırın.'],
            ],
        ],
        'social' => [
            'title' => 'Sosyal Medya', 'icon' => 'instagram',
            'fields' => [
                ['name' => 'social_facebook', 'label' => 'Facebook', 'type' => 'url', 'max' => 255],
                ['name' => 'social_instagram', 'label' => 'Instagram', 'type' => 'url', 'max' => 255],
                ['name' => 'social_twitter', 'label' => 'X (Twitter)', 'type' => 'url', 'max' => 255],
                ['name' => 'social_youtube', 'label' => 'YouTube', 'type' => 'url', 'max' => 255],
                ['name' => 'social_linkedin', 'label' => 'LinkedIn', 'type' => 'url', 'max' => 255],
                ['name' => 'social_whatsapp', 'label' => 'WhatsApp (https://wa.me/90...)', 'type' => 'url', 'max' => 255],
            ],
        ],
    ];
}
