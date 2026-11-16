<?php
/** @var string $title @var string $eyebrow @var string $lead @var array $crumbs [label => url|null] @var string $image */
$image = $image ?? '';
?>
<section class="page-banner<?= $image !== '' ? ' has-image' : '' ?>">
    <?php if ($image !== ''): ?>
        <img class="page-banner-image" src="<?= e(media_url($image)) ?>" alt="">
    <?php endif; ?>
    <div class="page-banner-shade"></div>
    <div class="wrap page-banner-inner">
        <nav class="breadcrumb" aria-label="Konumunuz">
            <a href="<?= e(url()) ?>">Ana Sayfa</a>
            <?php foreach (($crumbs ?? []) as $label => $href): ?>
                <span aria-hidden="true">/</span>
                <?php if ($href): ?><a href="<?= e($href) ?>"><?= e($label) ?></a><?php else: ?><span aria-current="page"><?= e($label) ?></span><?php endif; ?>
            <?php endforeach; ?>
        </nav>
        <?php if (!empty($eyebrow)): ?><p class="banner-note"><?= e($eyebrow) ?></p><?php endif; ?>
        <h1 class="banner-title"><?= e($title) ?></h1>
        <?php if (!empty($lead)): ?><p class="page-lead"><?= e($lead) ?></p><?php endif; ?>
    </div>
</section>
