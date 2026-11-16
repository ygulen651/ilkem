<?php /** @var array $projects @var ?string $status */
partial('page-banner', [
    'title' => 'Projeler', 'eyebrow' => 'Bir fikirle başlar',
    'lead' => setting('projects_lead', 'Gönüllülerimizle birlikte hayata geçirdiğimiz ve sürdürdüğümüz projeler.'),
    'crumbs' => ['Projeler' => null],
]);
?>
<section class="section wrap">
    <div class="filters" role="navigation" aria-label="Proje durumu">
        <a href="<?= e(url('projeler')) ?>"<?= $status === null ? ' class="selected" aria-current="page"' : '' ?>>Tümü</a>
        <?php foreach (PROJECT_STATUSES as $key => $label): ?>
            <a href="<?= e(url('projeler?durum=' . $key)) ?>"<?= $status === $key ? ' class="selected" aria-current="page"' : '' ?>><?= e($label) ?></a>
        <?php endforeach; ?>
    </div>
    <?php if ($projects !== []): ?>
        <div class="project-grid project-grid-2">
            <?php foreach ($projects as $project) { partial('project-card', ['project' => $project]); } ?>
        </div>
    <?php else: ?>
        <?php partial('empty', ['title' => 'Bu kategoride proje bulunamadı', 'text' => 'Farklı bir filtre seçebilir veya daha sonra tekrar bakabilirsiniz.', 'icon' => 'lightbulb']); ?>
    <?php endif; ?>
</section>
<?php partial('volunteer'); ?>
