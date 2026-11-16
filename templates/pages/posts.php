<?php /** @var array $posts @var ?string $type @var array $pager @var string $pageTitle */
partial('page-banner', [
    'title' => $pageTitle, 'eyebrow' => 'Güncel gelişmeler',
    'lead' => 'Derneğimizden haberler, etkinlik duyuruları ve gelişmeler.',
    'crumbs' => [$pageTitle => null],
]);
?>
<section class="section wrap">
    <div class="filters" role="navigation" aria-label="İçerik türü">
        <a href="<?= e(url('haberler')) ?>"<?= $type === null ? ' class="selected" aria-current="page"' : '' ?>>Tümü</a>
        <a href="<?= e(url('haberler?tur=haber')) ?>"<?= $type === 'haber' ? ' class="selected" aria-current="page"' : '' ?>>Haberler</a>
        <a href="<?= e(url('haberler?tur=duyuru')) ?>"<?= $type === 'duyuru' ? ' class="selected" aria-current="page"' : '' ?>>Duyurular</a>
    </div>
    <?php if ($posts !== []): ?>
        <div class="post-grid">
            <?php foreach ($posts as $post) { partial('post-card', ['post' => $post]); } ?>
        </div>
        <?php partial('pagination', ['pager' => $pager, 'baseUrl' => url('haberler'), 'params' => ['tur' => $type]]); ?>
    <?php else: ?>
        <?php partial('empty', ['title' => 'Gösterilecek içerik yok', 'text' => 'Bu bölümde henüz yayınlanmış bir içerik bulunmuyor.', 'icon' => 'megaphone']); ?>
    <?php endif; ?>
</section>
