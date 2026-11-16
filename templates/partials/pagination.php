<?php
/** @var array $pager @var string $baseUrl @var array $params */
if ($pager['pages'] <= 1) {
    return;
}
$params = $params ?? [];
$link = static function (int $page) use ($baseUrl, $params): string {
    $q = array_filter($params + ['sayfa' => $page > 1 ? $page : null], static fn ($v) => $v !== null && $v !== '');
    return $baseUrl . ($q ? '?' . http_build_query($q) : '');
};
?>
<nav class="pagination" aria-label="Sayfalar">
    <?php if ($pager['page'] > 1): ?>
        <a href="<?= e($link($pager['page'] - 1)) ?>" rel="prev" aria-label="Önceki sayfa"><?= icon('arrow-left') ?></a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $pager['pages']; $i++): ?>
        <?php if ($i === 1 || $i === $pager['pages'] || abs($i - $pager['page']) <= 2): ?>
            <a href="<?= e($link($i)) ?>"<?= $i === $pager['page'] ? ' class="current" aria-current="page"' : '' ?>><?= $i ?></a>
        <?php elseif (abs($i - $pager['page']) === 3): ?>
            <span class="dots">…</span>
        <?php endif; ?>
    <?php endfor; ?>
    <?php if ($pager['page'] < $pager['pages']): ?>
        <a href="<?= e($link($pager['page'] + 1)) ?>" rel="next" aria-label="Sonraki sayfa"><?= icon('arrow-right') ?></a>
    <?php endif; ?>
</nav>
