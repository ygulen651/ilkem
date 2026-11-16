<?php /** @var array $items @var array $pager */
partial('page-banner', [
    'title' => 'Fotoğraf Galerisi', 'eyebrow' => 'Birlikte geçen anlar',
    'lead' => 'Etkinliklerimizden, buluşmalarımızdan ve projelerimizden kareler.',
    'crumbs' => ['Galeri' => null],
]);
$videoId = youtube_id(setting('home_video_url'));
?>
<section class="section wrap">
    <?php if ($videoId && $pager['page'] === 1): ?>
        <div class="video-card video-card-wide">
            <button type="button" class="video-poster" data-youtube="<?= e($videoId) ?>" aria-label="Tanıtım videosunu oynat">
                <img src="<?= e(has_media(setting('home_video_image')) ? media_url(setting('home_video_image')) : 'https://i.ytimg.com/vi/' . $videoId . '/hqdefault.jpg') ?>" alt="" loading="lazy">
                <span class="play-button"><?= icon('play') ?></span>
                <span class="video-label"><?= e(setting('home_video_title', 'Tanıtım filmimizi izleyin')) ?></span>
            </button>
        </div>
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
