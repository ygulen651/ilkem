<?php /** @var string $key @var array $res @var array $record @var ?int $id @var array $errors */ ?>
<p><a class="back" href="<?= e(admin_url('list', ['res' => $key])) ?>">← <?= e($res['title']) ?></a></p>
<?php if ($errors !== []): ?><div class="alert alert-error" role="alert">Kayıt yapılamadı. Lütfen işaretli alanları kontrol edin.</div><?php endif; ?>
<form method="post" action="<?= e(admin_url('save', ['res' => $key, 'id' => $id])) ?>" enctype="multipart/form-data" class="panel form-grid" data-dirty-check>
    <?= csrf_field() ?>
    <input type="hidden" name="MAX_FILE_SIZE" value="<?= upload_max_bytes() ?>">
    <?php foreach ($res['fields'] as $field): ?>
        <?= render_field($field, $record[$field['name']] ?? '', $errors[$field['name']] ?? null) ?>
    <?php endforeach; ?>
    <div class="form-actions">
        <button class="btn btn-primary" type="submit">Kaydet</button>
        <button class="btn" type="submit" name="after" value="new">Kaydet ve yeni ekle</button>
        <?php if ($id && !empty($res['public']) && !empty($record['slug']) && (int) ($record['is_published'] ?? 0) === 1): ?>
            <a class="btn btn-ghost" href="<?= e(url($res['public'] . $record['slug'])) ?>" target="_blank" rel="noopener">Sitede görüntüle ↗</a>
        <?php endif; ?>
    </div>
</form>
<?php if ($id): ?>
    <form method="post" action="<?= e(admin_url('delete', ['res' => $key])) ?>" class="danger-zone" data-confirm="Bu kaydı kalıcı olarak silmek istediğinize emin misiniz?">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $id ?>">
        <button class="btn btn-danger" type="submit">Bu kaydı sil</button>
    </form>
<?php endif; ?>
