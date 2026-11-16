<?php /** @var array $admin @var array $counts @var array $resources @var int $unread @var array $latestMessages */ ?>
<p class="lead">Hoş geldiniz, <strong><?= e($admin['username']) ?></strong>.<?php if (!empty($admin['last_login_at'])): ?> <span class="muted">Son giriş: <?= e(format_date($admin['last_login_at'], true)) ?></span><?php endif; ?></p>
<div class="stat-grid">
    <a class="stat-card stat-accent" href="<?= e(admin_url('messages', ['f' => 'okunmamis'])) ?>">
        <?= icon('mail') ?><strong><?= $unread ?></strong><span>Okunmamış mesaj</span>
    </a>
    <?php foreach ($resources as $key => $res): ?>
        <a class="stat-card" href="<?= e(admin_url('list', ['res' => $key])) ?>">
            <?= icon($res['icon']) ?><strong><?= $counts[$key]['total'] ?></strong><span><?= e($res['title']) ?> <small>(<?= $counts[$key]['published'] ?> yayında)</small></span>
        </a>
    <?php endforeach; ?>
</div>
<div class="grid-2">
    <section class="panel">
        <div class="panel-head"><h2>Son mesajlar</h2><a href="<?= e(admin_url('messages')) ?>">Tümü →</a></div>
        <?php if ($latestMessages === []): ?>
            <p class="empty">Henüz mesaj yok.</p>
        <?php else: ?>
            <ul class="simple-list">
                <?php foreach ($latestMessages as $m): ?>
                    <li class="<?= (int) $m['is_read'] === 0 ? 'unread' : '' ?>"><a href="<?= e(admin_url('message', ['id' => $m['id']])) ?>"><strong><?= e($m['name']) ?></strong><span><?= e($m['subject']) ?></span><time><?= e(format_date($m['created_at'], true)) ?></time></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <section class="panel">
        <div class="panel-head"><h2>Hızlı işlemler</h2></div>
        <div class="quick">
            <a class="btn btn-primary" href="<?= e(admin_url('edit', ['res' => 'posts'])) ?>">+ Haber / duyuru ekle</a>
            <a class="btn" href="<?= e(admin_url('bulk')) ?>">+ Galeriye fotoğraf yükle</a>
            <a class="btn" href="<?= e(admin_url('edit', ['res' => 'projects'])) ?>">+ Proje ekle</a>
            <a class="btn" href="<?= e(admin_url('edit', ['res' => 'activities'])) ?>">+ Faaliyet ekle</a>
            <a class="btn" href="<?= e(admin_url('settings', ['g' => 'general'])) ?>">Logo ve site adı</a>
            <a class="btn" href="<?= e(admin_url('settings', ['g' => 'contact'])) ?>">İletişim bilgileri</a>
        </div>
    </section>
</div>
