<?php /** @var array $admin @var array $errors */ ?>
<div class="grid-2">
    <section class="panel">
        <h2>Hesap bilgileri</h2>
        <dl class="dl">
            <dt>Kullanıcı adı</dt><dd><?= e($admin['username']) ?></dd>
            <dt>E-posta</dt><dd><?= e($admin['email']) ?></dd>
            <dt>Son giriş</dt><dd><?= e(format_date($admin['last_login_at'], true) ?: '-') ?></dd>
        </dl>
        <p class="muted small">Yeni yönetici eklemek için sunucuda <code>php bin/create-admin.php</code> komutunu kullanabilirsiniz (README.md).</p>
    </section>
    <section class="panel">
        <h2>Şifre değiştir</h2>
        <?php if ($errors !== []): ?><div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" action="<?= e(admin_url('account')) ?>" class="stack">
            <?= csrf_field() ?>
            <div class="field"><label for="current_password">Mevcut şifre</label><input id="current_password" name="current_password" type="password" required autocomplete="current-password"></div>
            <div class="field"><label for="new_password">Yeni şifre</label><input id="new_password" name="new_password" type="password" required minlength="10" autocomplete="new-password"><p class="help">En az 10 karakter; en az bir harf ve bir rakam.</p></div>
            <div class="field"><label for="new_password_confirm">Yeni şifre (tekrar)</label><input id="new_password_confirm" name="new_password_confirm" type="password" required minlength="10" autocomplete="new-password"></div>
            <button class="btn btn-primary" type="submit">Şifreyi güncelle</button>
        </form>
    </section>
</div>
