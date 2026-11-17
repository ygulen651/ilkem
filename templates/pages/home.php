<?php
/** @var array $slides @var array $announcements @var array $activities @var array $projects @var array $posts @var array $gallery */
if ($slides === []) {
    // Yönetim panelinde görsel yoksa varsayılan açılış alanı
    $slides = [[
        'kicker' => site_full_name(), 'title' => site_name(),
        'subtitle' => setting('site_tagline', 'İlk okul anılarını geleceğe taşıyoruz.'), 'image' => 'assets/img/community.jpg', 'video' => '',
        'button1_text' => 'Derneğimizi Tanıyın', 'button1_url' => '/hakkimizda',
        'button2_text' => 'Neler yapıyoruz?', 'button2_url' => '/faaliyetlerimiz',
    ]];
}
$videoId = has_promo_video();
$aboutImage = setting('about_image', 'assets/img/together.jpg');
$alphabet = ['Aa', 'Bb', 'Cc', 'Çç', 'Dd', 'Ee', 'Ff', 'Gg', 'Ğğ', 'Hh', 'Iı', 'İi', 'Jj', 'Kk', 'Ll', 'Mm', 'Nn', 'Oo', 'Öö', 'Pp', 'Rr', 'Ss', 'Şş', 'Tt', 'Uu', 'Üü', 'Vv', 'Yy', 'Zz'];
/** Başlığı kelime kelime animasyon için böler. */
$words = static function (string $text): string {
    $out = [];
    foreach (preg_split('/\s+/u', trim($text)) ?: [] as $w) {
        $out[] = '<span class="w"><span>' . e($w) . '</span></span>';
    }
    return implode(' ', $out);
};
$stats = [];
for ($s = 1; $s <= 3; $s++) {
    if (setting("stat{$s}_value") !== '') {
        $stats[] = [setting("stat{$s}_value"), setting("stat{$s}_label")];
    }
}
?>
<section class="hero" aria-roledescription="carousel" aria-label="Öne çıkanlar" data-slider>
    <?php foreach ($slides as $i => $slide): ?>
        <div class="hero-slide<?= $i === 0 ? ' is-active' : '' ?>" role="group" aria-roledescription="slide" aria-label="<?= $i + 1 ?> / <?= count($slides) ?>"<?= $i === 0 ? '' : ' aria-hidden="true"' ?>>
            <?php $video = trim((string) ($slide['video'] ?? '')); ?>
            <?php if ($video !== '' && preg_match('#\.(mp4|webm)$#i', $video)): ?>
                <video class="hero-media" autoplay muted loop playsinline poster="<?= e(media_url($slide['image'], 'assets/img/community.jpg')) ?>">
                    <source src="<?= e(link_url($video)) ?>" type="video/<?= str_ends_with(strtolower($video), '.webm') ? 'webm' : 'mp4' ?>">
                </video>
            <?php else: ?>
                <img class="hero-media" src="<?= e(media_url($slide['image'], 'assets/img/community.jpg')) ?>" alt=""<?= $i === 0 ? ' fetchpriority="high"' : ' loading="lazy"' ?>>
            <?php endif; ?>
            <div class="hero-shade"></div>
            <div class="hero-content wrap">
                <?php if (!empty($slide['kicker'])): ?>
                    <p class="hero-note"><span><?= e($slide['kicker']) ?></span>
                        <svg class="chalk-line" viewBox="0 0 300 14" preserveAspectRatio="none" aria-hidden="true"><path d="M2 9c40-6 80-7 120-4s90 5 176-3"/></svg>
                    </p>
                <?php endif; ?>
                <?php $tag = $i === 0 ? 'h1' : 'h2'; ?>
                <<?= $tag ?> class="hero-title"><?= $words((string) $slide['title']) ?></<?= $tag ?>>
                <?php if (!empty($slide['subtitle'])): ?><p class="hero-text"><?= nl2br(e($slide['subtitle'])) ?></p><?php endif; ?>
                <div class="hero-actions">
                    <?php if (!empty($slide['button1_text'])): ?>
                        <a class="button" href="<?= e(link_url($slide['button1_url'])) ?>"<?= is_external_link((string) $slide['button1_url']) ? ' target="_blank" rel="noopener"' : '' ?>><?= e($slide['button1_text']) ?></a>
                    <?php endif; ?>
                    <?php if (!empty($slide['button2_text'])): ?>
                        <a class="hero-secondary" href="<?= e(link_url($slide['button2_url'])) ?>"<?= is_external_link((string) $slide['button2_url']) ? ' target="_blank" rel="noopener"' : '' ?>><span class="round-arrow"><?= icon('arrow-down') ?></span><?= e($slide['button2_text']) ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="hero-bottom wrap">
        <span><?= e(setting('hero_bottom_text', 'Her okul hatırası, ortak hafızamızın bir sayfası.')) ?></span>
        <?php if (count($slides) > 1): ?>
            <div class="hero-dots" aria-label="Slayt seçimi">
                <?php foreach ($slides as $i => $slide): ?>
                    <button type="button" class="hero-dot<?= $i === 0 ? ' is-active' : '' ?>" data-slide="<?= $i ?>" aria-label="<?= $i + 1 ?>. slayt"><span></span></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<div class="alphabet" aria-hidden="true">
    <div class="alphabet-track">
        <?php for ($copy = 0; $copy < 2; $copy++): ?>
            <ul>
                <?php foreach ($alphabet as $letter): ?><li><?= e($letter) ?></li><?php endforeach; ?>
            </ul>
        <?php endfor; ?>
    </div>
</div>

<aside class="announcement" aria-label="Duyurular">
    <div class="wrap announcement-inner">
        <a class="announcement-label" href="<?= e(url('haberler?tur=duyuru')) ?>"><?= icon('megaphone') ?>Duyurular</a>
        <?php if ($announcements !== []): ?>
            <ul class="announcement-list" data-ticker>
                <?php foreach ($announcements as $i => $a): ?>
                    <li<?= $i === 0 ? ' class="is-active"' : '' ?>>
                        <a href="<?= e(url('haberler/' . $a['slug'])) ?>"><time><?= e(format_date($a['published_at'])) ?></time><?= e($a['title']) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="announcement-empty">Şu anda yayında bir duyuru yok.</p>
        <?php endif; ?>
    </div>
</aside>

<section class="section wrap about" id="hakkimizda">
    <figure class="photo-print">
        <span class="tape" aria-hidden="true"></span>
        <img src="<?= e(media_url($aboutImage, 'assets/img/together.jpg')) ?>" alt="" loading="lazy">
        <figcaption><?= e(setting('about_note', 'Bir kara tahta, bir kuşağın hikâyesidir.')) ?></figcaption>
    </figure>
    <div class="about-copy">
        <h2 class="scribble"><?= e(setting('about_heading', 'Okul hatıraları, ortak hafızamız.')) ?></h2>
        <div class="about-text"><?= render_text(setting('about_summary', 'İlkem, İlk Eğitim Müzesi Derneği’nin kısaltmasıdır. İlköğretim mirasımızı topluyor, koruyor ve sergiliyoruz.')) ?></div>
        <?php if ($stats !== []): ?>
            <div class="report-card">
                <p class="report-title">Karnemiz</p>
                <dl>
                    <?php foreach ($stats as [$value, $label]): ?>
                        <div><dt><?= e($label) ?></dt><dd><?= e($value) ?></dd></div>
                    <?php endforeach; ?>
                </dl>
            </div>
        <?php endif; ?>
        <a class="text-link" href="<?= e(url('hakkimizda')) ?>">Derneğimizi yakından tanıyın</a>
    </div>
</section>

<section class="section-band paper-band">
    <div class="wrap section">
        <div class="section-heading">
            <h2 class="scribble"><?= e(setting('home_activities_title', 'Eğitim mirasını yaşatan çalışmalar.')) ?></h2>
            <a class="text-link" href="<?= e(url('faaliyetlerimiz')) ?>">Tüm faaliyetler</a>
        </div>
        <?php if ($activities !== []): ?>
            <div class="activity-grid">
                <?php foreach ($activities as $activity) { partial('activity-card', ['activity' => $activity]); } ?>
            </div>
        <?php else: ?>
            <?php partial('empty', ['title' => 'Faaliyetler yakında burada', 'text' => 'Faaliyet alanlarımızı kısa süre içinde ekleyeceğiz.', 'icon' => 'book']); ?>
        <?php endif; ?>
    </div>
</section>

<section class="section wrap">
    <div class="section-heading">
        <h2 class="scribble"><?= e(setting('home_projects_title', 'Geçmişin sınıfları, geleceğin hafızası.')) ?></h2>
        <a class="text-link" href="<?= e(url('projeler')) ?>">Tüm projeler</a>
    </div>
    <?php if ($projects !== []): ?>
        <div class="project-grid project-grid-<?= min(3, count($projects)) ?>">
            <?php foreach ($projects as $project) { partial('project-card', ['project' => $project]); } ?>
        </div>
    <?php else: ?>
        <?php partial('empty', ['title' => 'Henüz proje eklenmedi', 'text' => 'Projelerimizi yakında burada paylaşacağız.', 'icon' => 'lightbulb']); ?>
    <?php endif; ?>
</section>

<section class="section-band paper-band" id="guncel">
    <div class="wrap section news-split">
        <div class="section-heading section-heading-stack">
            <h2 class="scribble"><?= e(setting('home_news_title', 'Son haberler.')) ?></h2>
            <p class="muted">Müzeden gelişmeler, atölye takvimleri ve duyurular.</p>
            <a class="text-link" href="<?= e(url('haberler')) ?>">Tüm haberler</a>
        </div>
        <?php if ($posts !== []): ?>
            <ul class="notebook-list">
                <?php foreach ($posts as $post): ?>
                    <li>
                        <time datetime="<?= e(date('Y-m-d', strtotime($post['published_at']))) ?>"><b><?= e(date('j', strtotime($post['published_at']))) ?></b><?= e(mb_substr(format_date($post['published_at']), mb_strlen(date('j', strtotime($post['published_at']))) + 1)) ?></time>
                        <div>
                            <span class="badge badge-<?= e($post['type']) ?>"><?= e(POST_TYPES[$post['type']] ?? 'Haber') ?></span>
                            <h3><a href="<?= e(url('haberler/' . $post['slug'])) ?>"><?= e($post['title']) ?></a></h3>
                            <p><?= e(excerpt($post['summary'] ?: $post['content'], 140)) ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?php partial('empty', ['title' => 'Henüz haber yok', 'text' => 'Güncel gelişmeleri burada paylaşacağız.', 'icon' => 'megaphone']); ?>
        <?php endif; ?>
    </div>
</section>

<section class="section wrap media-section">
    <div class="section-heading">
        <h2 class="scribble"><?= e(setting('home_media_title', 'Müzeden kareler.')) ?></h2>
        <a class="text-link" href="<?= e(url('galeri')) ?>">Galeriye git</a>
    </div>
    <div class="media-grid<?= $videoId ? ' has-video' : '' ?>">
        <?php if ($videoId): ?>
            <?php partial('video-card', ['class' => '', 'defaultTitle' => 'Müzemizi tanıyın']); ?>
        <?php endif; ?>
        <?php if ($gallery !== []): ?>
            <div class="album" data-gallery>
                <?php foreach (array_slice($gallery, 0, $videoId ? 4 : 6) as $photo): ?>
                    <a href="<?= e(media_url($photo['image'])) ?>" class="gallery-item album-photo" data-caption="<?= e($photo['title']) ?>">
                        <img src="<?= e(media_url($photo['image'])) ?>" alt="<?= e($photo['title']) ?>" loading="lazy">
                        <?php if (!empty($photo['title'])): ?><span><?= e($photo['title']) ?></span><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php elseif (!$videoId): ?>
            <?php partial('empty', ['title' => 'Galeri hazırlanıyor', 'text' => 'Müzeden kareler yakında burada.', 'icon' => 'image']); ?>
        <?php endif; ?>
    </div>
</section>

<?php partial('volunteer'); ?>
