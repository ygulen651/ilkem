<?php /** @var array $item @var array $others */
partial('page-banner', [
    'title' => $item['title'], 'eyebrow' => 'Faaliyet alanı', 'lead' => $item['summary'] ?? '',
    'crumbs' => ['Faaliyetlerimiz' => url('faaliyetlerimiz'), $item['title'] => null],
]);
?>
<section class="section wrap detail-layout">
    <article class="detail-main">
        <?php if (has_media($item['image'])): ?>
            <img class="detail-image" src="<?= e(media_url($item['image'])) ?>" alt="<?= e($item['title']) ?>">
        <?php endif; ?>
        <div class="prose">
            <?php $html = render_text($item['content']); ?>
            <?= $html !== '' ? $html : '<p>' . e($item['summary'] ?: 'Bu faaliyetle ilgili ayrıntılar yakında eklenecek.') . '</p>' ?>
        </div>
        <div class="detail-actions">
            <a class="button" href="<?= e(url('iletisim?konu=gonullu')) ?>">Bu çalışmada gönüllü ol</a>
            <a class="text-link" href="<?= e(url('faaliyetlerimiz')) ?>"><?= icon('arrow-left') ?> Tüm faaliyetler</a>
        </div>
    </article>
    <aside class="detail-aside">
        <div class="aside-box">
            <h2>Diğer faaliyetler</h2>
            <?php if ($others !== []): ?>
                <ul class="aside-list">
                    <?php foreach ($others as $o): ?>
                        <li><a href="<?= e(url('faaliyetlerimiz/' . $o['slug'])) ?>"><span class="aside-icon"><?= icon($o['icon'] ?: 'star') ?></span><?= e($o['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="muted">Başka faaliyet bulunmuyor.</p>
            <?php endif; ?>
        </div>
        <div class="aside-box aside-cta">
            <h2>Bir sorunuz mu var?</h2>
            <p>Faaliyetlerimiz hakkında bilgi almak için bize yazın.</p>
            <a class="button" href="<?= e(url('iletisim')) ?>">İletişime geç</a>
        </div>
    </aside>
</section>
