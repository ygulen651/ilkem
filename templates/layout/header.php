<?php
/** @var string $pageTitle @var string $metaDescription @var string $activeNav @var string $ogImage */
$siteName = site_name();
$fullTitle = $pageTitle !== '' ? $pageTitle . ' | ' . $siteName : $siteName . ' | ' . setting('site_tagline', 'İlk okul anılarını geleceğe taşıyoruz');
$logo = setting('logo');
$brand = brand_parts();
$nav = [
    'home'       => ['Ana Sayfa', ''],
    'about'      => ['Hakkımızda', 'hakkimizda'],
    'activities' => ['Faaliyetlerimiz', 'faaliyetlerimiz'],
    'projects'   => ['Projeler', 'projeler'],
    'posts'      => ['Haberler', 'haberler'],
    'gallery'    => ['Galeri', 'galeri'],
    'contact'    => ['İletişim', 'iletisim'],
];
$ogImageUrl = $ogImage !== '' && has_media($ogImage) ? $ogImage : (setting('og_image') ?: 'assets/img/community.jpg');
$ogImageUrl = preg_match('#^https://#', $ogImageUrl) ? $ogImageUrl : absolute_url($ogImageUrl);
?><!doctype html>
<html lang="tr" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($fullTitle) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <meta name="theme-color" content="#22342d">
    <link rel="canonical" href="<?= e(absolute_url(current_path())) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= e($siteName) ?>">
    <meta property="og:title" content="<?= e($pageTitle !== '' ? $pageTitle : $siteName) ?>">
    <meta property="og:description" content="<?= e($metaDescription) ?>">
    <meta property="og:image" content="<?= e($ogImageUrl) ?>">
    <meta property="og:locale" content="tr_TR">
    <link rel="icon" href="<?= e($logo !== '' && has_media($logo) ? media_url($logo) : url('assets/img/logo.svg')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..800&family=Caveat:wght@500;700&display=swap">
    <link rel="stylesheet" href="<?= e(asset('assets/css/style.css')) ?>">
    <script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
</head>
<body>
<a class="skip-link" href="#icerik">İçeriğe geç</a>
<header class="header" id="site-header">
    <a class="brand" href="<?= e(url()) ?>" aria-label="<?= e($siteName) ?> ana sayfa">
        <?php if ($logo !== '' && has_media($logo)): ?>
            <img class="brand-logo" src="<?= e(media_url($logo)) ?>" alt="" height="51">
        <?php else: ?>
            <img class="brand-logo" src="<?= e(url('assets/img/logo.svg')) ?>" width="51" height="51" alt="">
        <?php endif; ?>
        <?php if (setting('logo_show_text', '1') === '1'): ?>
            <span class="brand-text<?= $brand[2] ? ' brand-long' : '' ?>"><?= e($brand[0]) ?><?php if ($brand[1] !== ''): ?><small><?= e($brand[1]) ?></small><?php endif; ?></span>
        <?php endif; ?>
    </a>
    <nav id="navigation" class="main-nav" aria-label="Ana menü">
        <?php foreach ($nav as $key => [$label, $href]): ?>
            <a href="<?= e(url($href)) ?>"<?= $activeNav === $key ? ' class="active" aria-current="page"' : '' ?>><?= e($label) ?></a>
        <?php endforeach; ?>
        <a class="button nav-cta" href="<?= e(link_url(setting('volunteer_button_url'), 'iletisim?konu=gonullu')) ?>">Gönüllü Ol</a>
    </nav>
    <a class="button header-cta" href="<?= e(link_url(setting('volunteer_button_url'), 'iletisim?konu=gonullu')) ?>">Gönüllü Ol</a>
    <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="navigation" aria-label="Menüyü aç"><span></span><span></span><span></span></button>
</header>
<main id="icerik">
<?php foreach (take_flashes() as $flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>" role="status"><div class="wrap"><?= e($flash['message']) ?></div></div>
<?php endforeach; ?>
