<?php /** @var array $activity */ ?>
<a href="<?= e(url('faaliyetlerimiz/' . $activity['slug'])) ?>" class="activity">
    <span class="activity-icon"><?= icon($activity['icon'] ?: 'star') ?></span>
    <h3><?= e($activity['title']) ?></h3>
    <p><?= e(excerpt($activity['summary'] ?: $activity['content'], 140)) ?></p>
    <span class="activity-link">Ayrıntılar</span>
</a>
