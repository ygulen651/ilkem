<?php /** @var array $project */ ?>
<a class="project" href="<?= e(url('projeler/' . $project['slug'])) ?>">
    <img src="<?= e(media_url($project['image'])) ?>" alt="" loading="lazy">
    <span class="stamp stamp-<?= e($project['status']) ?>"><?= e(PROJECT_STATUSES[$project['status']] ?? 'Proje') ?></span>
    <div class="project-overlay">
        <?php if (!empty($project['category'])): ?><span class="project-tag"><?= e($project['category']) ?></span><?php endif; ?>
        <h3><?= e($project['title']) ?></h3>
        <p><?= e(excerpt($project['summary'] ?: $project['content'], 110)) ?></p>
    </div>
</a>
