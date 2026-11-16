<?php /** @var array $item @var array $others */
partial('page-banner', [
    'title' => $item['title'], 'eyebrow' => $item['category'] ?: 'Proje', 'lead' => $item['summary'] ?? '',
    'crumbs' => ['Projeler' => url('projeler'), $item['title'] => null],
    'image' => has_media($item['image']) ? $item['image'] : '',
]);
?>
<section class="section wrap detail-layout">
    <article class="detail-main">
        <div class="prose">
            <?php $html = render_text($item['content']); ?>
            <?= $html !== '' ? $html : '<p>' . e($item['summary'] ?: 'Bu projeyle ilgili ayrıntılar yakında eklenecek.') . '</p>' ?>
        </div>
        <div class="detail-actions">
            <a class="button" href="<?= e(url('iletisim?konu=is-birligi')) ?>">Projeye destek ol</a>
            <a class="text-link" href="<?= e(url('projeler')) ?>"><?= icon('arrow-left') ?> Tüm projeler</a>
        </div>
    </article>
    <aside class="detail-aside">
        <div class="aside-box">
            <h2>Proje bilgileri</h2>
            <dl class="meta-list">
                <div><dt><?= icon('tag') ?> Durum</dt><dd><span class="status status-<?= e($item['status']) ?>"><?= e(PROJECT_STATUSES[$item['status']] ?? '-') ?></span></dd></div>
                <?php if (!empty($item['category'])): ?><div><dt><?= icon('star') ?> Alan</dt><dd><?= e($item['category']) ?></dd></div><?php endif; ?>
                <?php if (!empty($item['location'])): ?><div><dt><?= icon('map-pin') ?> Yer</dt><dd><?= e($item['location']) ?></dd></div><?php endif; ?>
                <?php if (!empty($item['start_date'])): ?><div><dt><?= icon('calendar') ?> Başlangıç</dt><dd><?= e(format_date($item['start_date'])) ?></dd></div><?php endif; ?>
                <?php if (!empty($item['end_date'])): ?><div><dt><?= icon('calendar') ?> Bitiş</dt><dd><?= e(format_date($item['end_date'])) ?></dd></div><?php endif; ?>
            </dl>
        </div>
        <?php if ($others !== []): ?>
            <div class="aside-box">
                <h2>Diğer projeler</h2>
                <ul class="aside-list">
                    <?php foreach ($others as $o): ?>
                        <li><a href="<?= e(url('projeler/' . $o['slug'])) ?>"><img src="<?= e(media_url($o['image'])) ?>" alt="" loading="lazy"><?= e($o['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </aside>
</section>
