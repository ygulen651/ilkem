<?php
/** @var string $key @var array $res @var array $rows @var array $pager @var string $q @var ?string $filter @var string $status */
$selfUrl = admin_url('list', ['res' => $key, 'q' => $q, 'f' => $filter, 'durum' => $status, 'sayfa' => $pager['page'] > 1 ? $pager['page'] : null]);
?>
<?php if (!empty($res['description'])): ?><p class="lead muted"><?= e($res['description']) ?></p><?php endif; ?>
<div class="toolbar">
    <form method="get" action="<?= e(url('admin/')) ?>" class="search">
        <input type="hidden" name="r" value="list">
        <input type="hidden" name="res" value="<?= e($key) ?>">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Ara…" aria-label="Ara">
        <?php if (!empty($res['filter'])): ?>
            <select name="f" aria-label="Tür">
                <option value="">Tüm türler</option>
                <?php foreach ($res['filter']['options'] as $k => $label): ?>
                    <option value="<?= e($k) ?>"<?= $filter === $k ? ' selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
        <select name="durum" aria-label="Yayın durumu">
            <option value="">Tüm durumlar</option>
            <option value="yayinda"<?= $status === 'yayinda' ? ' selected' : '' ?>>Yayında</option>
            <option value="taslak"<?= $status === 'taslak' ? ' selected' : '' ?>>Taslak</option>
        </select>
        <button class="btn" type="submit">Filtrele</button>
    </form>
    <div class="toolbar-actions">
        <?php if (!empty($res['bulk_upload'])): ?><a class="btn" href="<?= e(admin_url('bulk')) ?>">Toplu yükle</a><?php endif; ?>
        <a class="btn btn-primary" href="<?= e(admin_url('edit', ['res' => $key])) ?>">+ Yeni <?= e(mb_strtolower($res['singular'])) ?></a>
    </div>
</div>

<?php if ($rows === []): ?>
    <div class="panel empty-panel">
        <?= icon($res['icon'], 'icon empty-icon') ?>
        <h2><?= $q !== '' || $filter || $status ? 'Aramanıza uygun kayıt bulunamadı' : 'Henüz kayıt yok' ?></h2>
        <p class="muted">İlk kaydı eklemek için “Yeni” butonunu kullanın.</p>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <?php foreach ($res['columns'] as $col => $label): ?><th scope="col"><?= e($label) ?></th><?php endforeach; ?>
                    <th scope="col">Durum</th>
                    <th scope="col" class="actions-col"><span class="sr-only">İşlemler</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($res['columns'] as $col => $label): ?>
                            <td data-label="<?= e($label) ?>">
                                <?php if ($col === 'image'): ?>
                                    <img class="thumb" src="<?= e(media_url($row['image'])) ?>" alt="" loading="lazy">
                                <?php elseif ($col === 'title'): ?>
                                    <a class="row-title" href="<?= e(admin_url('edit', ['res' => $key, 'id' => $row['id']])) ?>"><?= e($row['title'] !== '' && $row['title'] !== null ? $row['title'] : '(başlıksız)') ?></a>
                                    <?php if (!empty($res['public']) && !empty($row['slug'])): ?><small class="muted">/<?= e($res['public'] . $row['slug']) ?></small><?php endif; ?>
                                <?php elseif ($col === 'type'): ?>
                                    <span class="pill pill-<?= e($row['type']) ?>"><?= e(POST_TYPES[$row['type']] ?? $row['type']) ?></span>
                                <?php elseif ($col === 'status'): ?>
                                    <?= e(PROJECT_STATUSES[$row['status']] ?? $row['status']) ?>
                                <?php elseif ($col === 'is_featured'): ?>
                                    <?= (int) $row['is_featured'] === 1 ? '★ Evet' : '—' ?>
                                <?php elseif ($col === 'published_at'): ?>
                                    <?= e(format_date($row['published_at'], true)) ?><?php if (strtotime($row['published_at']) > time()): ?> <span class="pill pill-off">İleri tarihli</span><?php endif; ?>
                                <?php else: ?>
                                    <?= e($row[$col] ?? '') ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <td data-label="Durum"><?= status_badge($row['is_published']) ?></td>
                        <td class="actions">
                            <a class="btn btn-sm" href="<?= e(admin_url('edit', ['res' => $key, 'id' => $row['id']])) ?>">Düzenle</a>
                            <form method="post" action="<?= e(admin_url('toggle', ['res' => $key])) ?>">
                                <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><input type="hidden" name="back" value="<?= e($selfUrl) ?>">
                                <button class="btn btn-sm" type="submit"><?= (int) $row['is_published'] === 1 ? 'Yayından kaldır' : 'Yayınla' ?></button>
                            </form>
                            <form method="post" action="<?= e(admin_url('delete', ['res' => $key])) ?>" data-confirm="Bu kaydı kalıcı olarak silmek istediğinize emin misiniz?">
                                <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><input type="hidden" name="back" value="<?= e($selfUrl) ?>">
                                <button class="btn btn-sm btn-danger" type="submit">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pager['pages'] > 1): ?>
        <nav class="pager" aria-label="Sayfalar">
            <?php for ($i = 1; $i <= $pager['pages']; $i++): ?>
                <a href="<?= e(admin_url('list', ['res' => $key, 'q' => $q, 'f' => $filter, 'durum' => $status, 'sayfa' => $i])) ?>"<?= $i === $pager['page'] ? ' class="current"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
    <p class="muted small">Toplam <?= $pager['total'] ?> kayıt.</p>
<?php endif; ?>
