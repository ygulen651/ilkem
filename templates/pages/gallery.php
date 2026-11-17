<?php /** @var array $items @var array $pager */
partial('page-banner', [
    'title' => 'Fotoğraf Galerisi', 'eyebrow' => 'Birlikte geçen anlar',
    'lead' => 'Etkinliklerimizden, buluşmalarımızdan ve projelerimizden kareler.',
    'crumbs' => ['Galeri' => null],
]);
?>
<section class="section wrap">
    <?php if (has_promo_video() && $pager['page'] === 1): ?>
        <?php partial('video-card', ['class' => 'video-card-wide', 'defaultTitle' => 'Tanıtım filmimizi izleyin']); ?>
    <?php endif; ?>
    <?php if ($items !== []): ?>
        <div class="gallery-grid" data-gallery>
            <?php foreach ($items as $photo): ?>
                <figure class="gallery-figure">
                    <a href="<?= e(media_url($photo['image'])) ?>" class="gallery-item" data-caption="<?= e($photo['title']) ?>">
                        <img src="<?= e(media_url($photo['image'])) ?>" alt="<?= e($photo['title']) ?>" loading="lazy">
                    </a>
                    <?php if (!empty($photo['title'])): ?><figcaption><?= e($photo['title']) ?></figcaption><?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
        <?php partial('pagination', ['pager' => $pager, 'baseUrl' => url('galeri')]); ?>
    <?php else: ?>
        <?php partial('empty', ['title' => 'Galeri henüz boş', 'text' => 'Etkinliklerimizden fotoğraflar yakında burada olacak.', 'icon' => 'image']); ?>
    <?php endif; ?>
</section>
