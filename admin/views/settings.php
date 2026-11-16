<?php /** @var array $groups @var string $group @var array $def @var array $values @var array $errors */ ?>
<nav class="tabs" aria-label="Ayar grupları">
    <?php foreach ($groups as $g => $gd): ?>
        <a href="<?= e(admin_url('settings', ['g' => $g])) ?>"<?= $g === $group ? ' class="active" aria-current="page"' : '' ?>><?= e($gd['title']) ?></a>
    <?php endforeach; ?>
</nav>
<?php if ($errors !== []): ?><div class="alert alert-error" role="alert">Kaydedilemedi. Lütfen işaretli alanları kontrol edin.</div><?php endif; ?>
<form method="post" action="<?= e(admin_url('settings', ['g' => $group])) ?>" enctype="multipart/form-data" class="panel form-grid" data-dirty-check>
    <?= csrf_field() ?>
    <input type="hidden" name="MAX_FILE_SIZE" value="<?= upload_max_bytes() ?>">
    <?php foreach ($def['fields'] as $field): ?>
        <?= render_field($field, $values[$field['name']] ?? '', $errors[$field['name']] ?? null) ?>
    <?php endforeach; ?>
    <div class="form-actions"><button class="btn btn-primary" type="submit">Kaydet</button></div>
</form>
