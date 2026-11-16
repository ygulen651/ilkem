<?php /** @var array $msg */ ?>
<p><a class="back" href="<?= e(admin_url('messages')) ?>">← Mesajlar</a></p>
<article class="panel message">
    <header class="message-head">
        <div>
            <h2><?= e($msg['subject'] ?: 'Konu belirtilmemiş') ?></h2>
            <p><strong><?= e($msg['name']) ?></strong> · <a href="mailto:<?= e($msg['email']) ?>"><?= e($msg['email']) ?></a><?php if ($msg['phone']): ?> · <a href="<?= e(tel_href($msg['phone'])) ?>"><?= e($msg['phone']) ?></a><?php endif; ?></p>
        </div>
        <time><?= e(format_date($msg['created_at'], true)) ?></time>
    </header>
    <div class="message-body"><?= nl2br(e($msg['message'])) ?></div>
    <footer class="message-foot">
        <a class="btn btn-primary" href="mailto:<?= e($msg['email']) ?>?subject=<?= e(rawurlencode('Yanıt: ' . ($msg['subject'] ?? ''))) ?>">E-postayla yanıtla</a>
        <form method="post" action="<?= e(admin_url('message-action')) ?>">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $msg['id'] ?>"><input type="hidden" name="action" value="unread">
            <button class="btn" type="submit">Okunmadı olarak işaretle</button>
        </form>
        <form method="post" action="<?= e(admin_url('message-action')) ?>" data-confirm="Bu mesajı silmek istediğinize emin misiniz?">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $msg['id'] ?>"><input type="hidden" name="action" value="delete">
            <button class="btn btn-danger" type="submit">Sil</button>
        </form>
    </footer>
    <p class="muted small">IP: <?= e($msg['ip'] ?? '-') ?></p>
</article>
