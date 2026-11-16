<?php /** @var string $title @var string $text @var string $icon */ ?>
<div class="empty-state">
    <?= icon($icon ?? 'image', 'empty-icon') ?>
    <h3><?= e($title) ?></h3>
    <?php if (!empty($text)): ?><p><?= e($text) ?></p><?php endif; ?>
</div>
