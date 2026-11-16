<?php /** @var array $rows @var array $pager @var string $filter */ ?>
<div class="toolbar">
    <nav class="tabs tabs-inline">
        <a href="<?= e(admin_url('messages')) ?>"<?= $filter !== 'okunmamis' ? ' class="active"' : '' ?>>Tümü</a>
        <a href="<?= e(admin_url('messages', ['f' => 'okunmamis'])) ?>"<?= $filter === 'okunmamis' ? ' class="active"' : '' ?>>Okunmamış</a>
    </nav>
    <form method="post" action="<?= e(admin_url('message-action')) ?>">
        <?= csrf_field() ?><input type="hidden" name="action" value="read-all">
        <button class="btn" type="submit">Tümünü okundu say</button>
    </form>
</div>
<?php if ($rows === []): ?>
    <div class="panel empty-panel"><?= icon('mail', 'icon empty-icon') ?><h2>Mesaj yok</h2><p class="muted">İletişim formundan gelen mesajlar burada listelenir.</p></div>
<?php else: ?>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Gönderen</th><th>Konu</th><th>Mesaj</th><th>Tarih</th><th class="actions-col"><span class="sr-only">İşlemler</span></th></tr></thead>
            <tbody>
                <?php foreach ($rows as $m): ?>
                    <tr class="<?= (int) $m['is_read'] === 0 ? 'unread' : '' ?>">
                        <td data-label="Gönderen"><a class="row-title" href="<?= e(admin_url('message', ['id' => $m['id']])) ?>"><?= e($m['name']) ?></a><small class="muted"><?= e($m['email']) ?></small></td>
                        <td data-label="Konu"><?= e($m['subject']) ?></td>
                        <td data-label="Mesaj" class="muted"><?= e(excerpt($m['message'], 80)) ?></td>
                        <td data-label="Tarih"><?= e(format_date($m['created_at'], true)) ?></td>
                        <td class="actions">
                            <a class="btn btn-sm" href="<?= e(admin_url('message', ['id' => $m['id']])) ?>">Oku</a>
                            <form method="post" action="<?= e(admin_url('message-action')) ?>" data-confirm="Bu mesajı silmek istediğinize emin misiniz?">
                                <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $m['id'] ?>"><input type="hidden" name="action" value="delete">
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
                <a href="<?= e(admin_url('messages', ['f' => $filter, 'sayfa' => $i])) ?>"<?= $i === $pager['page'] ? ' class="current"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
