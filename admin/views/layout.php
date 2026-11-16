<?php
/** @var string $title @var string $nav @var string $content */
$unreadCount = unread_message_count();
$menu = [
    'Genel' => [
        'dashboard' => ['Genel Bakış', admin_url(), 'home'],
        'messages'  => ['Mesajlar', admin_url('messages'), 'mail', $unreadCount],
    ],
    'İçerik' => [
        'slides'     => ['Ana Sayfa Görselleri', admin_url('list', ['res' => 'slides']), 'image'],
        'settings-about' => ['Hakkımızda', admin_url('settings', ['g' => 'about']), 'users'],
        'activities' => ['Faaliyetler', admin_url('list', ['res' => 'activities']), 'sprout'],
        'projects'   => ['Projeler', admin_url('list', ['res' => 'projects']), 'lightbulb'],
        'posts'      => ['Haberler & Duyurular', admin_url('list', ['res' => 'posts']), 'megaphone'],
        'gallery'    => ['Fotoğraf Galerisi', admin_url('list', ['res' => 'gallery']), 'palette'],
    ],
    'Ayarlar' => [
        'settings-general' => ['Genel Ayarlar ve Logo', admin_url('settings', ['g' => 'general']), 'star'],
        'settings-home'    => ['Ana Sayfa Bölümleri', admin_url('settings', ['g' => 'home']), 'home'],
        'settings-contact' => ['İletişim Bilgileri', admin_url('settings', ['g' => 'contact']), 'phone'],
        'settings-social'  => ['Sosyal Medya', admin_url('settings', ['g' => 'social']), 'instagram'],
        'account'          => ['Hesabım', admin_url('account'), 'hand'],
    ],
];
?><!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title) ?> | <?= e(site_name()) ?> Yönetim</title>
    <link rel="icon" href="<?= e(url('assets/img/logo.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset('admin/assets/admin.css')) ?>">
    <script src="<?= e(asset('admin/assets/admin.js')) ?>" defer></script>
</head>
<body>
<div class="admin">
    <aside class="sidebar" id="sidebar">
        <a class="side-brand" href="<?= e(admin_url()) ?>"><img src="<?= e(url('assets/img/logo.svg')) ?>" width="38" height="38" alt=""><span><?= e(site_name()) ?><small>Yönetim Paneli</small></span></a>
        <nav aria-label="Yönetim menüsü">
            <?php foreach ($menu as $groupLabel => $items): ?>
                <p class="side-group"><?= e($groupLabel) ?></p>
                <?php foreach ($items as $key => $item): ?>
                    <a href="<?= e($item[1]) ?>"<?= $nav === $key ? ' class="active" aria-current="page"' : '' ?>><?= icon($item[2]) ?><span><?= e($item[0]) ?></span><?php if (!empty($item[3])): ?><b class="count"><?= (int) $item[3] ?></b><?php endif; ?></a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </nav>
    </aside>
    <div class="side-overlay" data-close-sidebar></div>
    <div class="main">
        <header class="topbar">
            <button type="button" class="side-toggle" aria-controls="sidebar" aria-expanded="false" aria-label="Menüyü aç"><span></span><span></span><span></span></button>
            <h1><?= e($title) ?></h1>
            <div class="topbar-actions">
                <a class="btn btn-ghost" href="<?= e(url()) ?>" target="_blank" rel="noopener"><?= icon('globe') ?><span>Siteyi görüntüle</span></a>
                <form method="post" action="<?= e(admin_url('logout')) ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-ghost" type="submit"><?= icon('arrow') ?><span>Çıkış</span></button>
                </form>
            </div>
        </header>
        <div class="content">
            <?php foreach (take_flashes() as $flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
            <?php endforeach; ?>
            <?= $content ?>
        </div>
    </div>
</div>
</body>
</html>
