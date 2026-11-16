<?php /** @var string $title @var string $content */ ?><!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title) ?> | <?= e(site_name()) ?> Yönetim</title>
    <link rel="icon" href="<?= e(url('assets/img/logo.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset('admin/assets/admin.css')) ?>">
</head>
<body class="auth-body">
<main class="auth-card">
    <a class="auth-brand" href="<?= e(url()) ?>"><img src="<?= e(url('assets/img/logo.svg')) ?>" width="48" height="48" alt=""><span><?= e(site_name()) ?><small>Yönetim Paneli</small></span></a>
    <?php foreach (take_flashes() as $flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
</main>
<script src="<?= e(asset('admin/assets/admin.js')) ?>" defer></script>
</body>
</html>
