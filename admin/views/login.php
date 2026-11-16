<?php /** @var ?string $error @var string $username */ ?>
<h1>Giriş yap</h1>
<p class="muted">Yönetim paneline erişmek için bilgilerinizi girin.</p>
<?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
<form method="post" action="<?= e(admin_url('login')) ?>" class="stack">
    <?= csrf_field() ?>
    <div class="field">
        <label for="username">Kullanıcı adı veya e-posta</label>
        <input id="username" name="username" autocomplete="username" required value="<?= e($username) ?>" autofocus>
    </div>
    <div class="field">
        <label for="password">Şifre</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
    </div>
    <button class="btn btn-primary btn-block" type="submit">Giriş yap</button>
</form>
<p class="auth-foot"><a href="<?= e(url()) ?>">← Siteye dön</a></p>
