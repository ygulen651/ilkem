<?php /** @var array $item @var array $others */
$typeLabel = POST_TYPES[$item['type']] ?? 'Haber';
partial('page-banner', [
    'title' => $item['title'], 'eyebrow' => $typeLabel, 'lead' => format_date($item['published_at']),
    'crumbs' => ['Haberler & Duyurular' => url('haberler'), $item['title'] => null],
]);
?>
<section class="section wrap detail-layout">
    <article class="detail-main">
        <?php if (has_media($item['image'])): ?>
            <img class="detail-image" src="<?= e(media_url($item['image'])) ?>" alt="<?= e($item['title']) ?>">
        <?php endif; ?>
        <?php if (!empty($item['summary'])): ?><p class="detail-summary"><?= e($item['summary']) ?></p><?php endif; ?>
        <div class="prose"><?= render_text($item['content']) ?></div>
        <div class="detail-actions">
            <a class="text-link" href="<?= e(url('haberler' . ($item['type'] === 'duyuru' ? '?tur=duyuru' : ''))) ?>"><?= icon('arrow-left') ?> Tüm <?= $item['type'] === 'duyuru' ? 'duyurular' : 'haberler' ?></a>
        </div>
    </article>
    <aside class="detail-aside">
        <div class="aside-box">
            <h2>Son paylaşımlar</h2>
            <?php if ($others !== []): ?>
                <ul class="aside-list aside-posts">
                    <?php foreach ($others as $o): ?>
                        <li><a href="<?= e(url('haberler/' . $o['slug'])) ?>"><span><time><?= e(format_date($o['published_at'])) ?></time><?= e($o['title']) ?></span></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="muted">Başka paylaşım bulunmuyor.</p>
            <?php endif; ?>
        </div>
    </aside>
</section>
