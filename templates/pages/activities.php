<?php /** @var array $activities */
partial('page-banner', [
    'title' => 'Faaliyetlerimiz', 'eyebrow' => 'Birlikte neler yapıyoruz?',
    'lead' => setting('activities_lead', 'Eğitimden kültüre, dayanışmadan çevreye; hayata dokunan çalışmalarımızı keşfedin.'),
    'crumbs' => ['Faaliyetlerimiz' => null],
]);
?>
<section class="section wrap">
    <?php if ($activities !== []): ?>
        <div class="activity-cards">
            <?php foreach ($activities as $item): ?>
                <article class="card">
                    <a class="card-media" href="<?= e(url('faaliyetlerimiz/' . $item['slug'])) ?>" tabindex="-1" aria-hidden="true">
                        <img src="<?= e(media_url($item['image'])) ?>" alt="" loading="lazy">
                        <span class="card-icon"><?= icon($item['icon'] ?: 'star') ?></span>
                    </a>
                    <div class="card-body">
                        <h2><a href="<?= e(url('faaliyetlerimiz/' . $item['slug'])) ?>"><?= e($item['title']) ?></a></h2>
                        <p><?= e(excerpt($item['summary'] ?: $item['content'], 150)) ?></p>
                        <a class="text-link" href="<?= e(url('faaliyetlerimiz/' . $item['slug'])) ?>" aria-label="<?= e($item['title']) ?> - ayrıntılar">Ayrıntılar</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <?php partial('empty', ['title' => 'Henüz faaliyet eklenmedi', 'text' => 'Faaliyet alanlarımız yakında burada yer alacak.', 'icon' => 'sprout']); ?>
    <?php endif; ?>
</section>
<?php partial('volunteer'); ?>
