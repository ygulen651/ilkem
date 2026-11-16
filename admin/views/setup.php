<?php /** @var bool $tokenOk @var array $errors @var array $old */ ?>
<h1>İlk yönetici hesabı</h1>
<?php if (!$tokenOk): ?>
    <div class="alert alert-info">
        Henüz yönetici hesabı yok. Güvenlik nedeniyle tarayıcıdan kurulum, bir <strong>kurulum anahtarı</strong> tanımlanana kadar kapalıdır.
    </div>
    <ol class="steps">
        <li><code>config/config.php</code> dosyasını açın.</li>
        <li><code>'setup_token'</code> değerine en az 24 karakterlik rastgele bir anahtar yazın.</li>
        <li>Bu sayfayı yenileyin ve anahtarla birlikte hesabınızı oluşturun.</li>
    </ol>
    <p class="muted">Alternatif: sunucuda SSH erişiminiz varsa <code>php bin/create-admin.php</code> komutunu kullanabilirsiniz.</p>
<?php else: ?>
    <p class="muted">Bu sayfa yalnızca hiç yönetici yokken açılır. Hesap oluşturulduktan sonra kendiliğinden kapanır.</p>
    <?php if ($errors !== []): ?>
        <div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>
    <form method="post" action="<?= e(admin_url('setup')) ?>" class="stack" autocomplete="off">
        <?= csrf_field() ?>
        <div class="field">
            <label for="setup_token">Kurulum anahtarı</label>
            <input id="setup_token" name="setup_token" type="password" required>
            <p class="help">config/config.php içindeki <code>setup_token</code> değeri.</p>
        </div>
        <div class="field">
            <label for="username">Kullanıcı adı</label>
            <input id="username" name="username" required pattern="[A-Za-z0-9_.\-]{3,50}" value="<?= e($old['username']) ?>" autocomplete="username">
        </div>
        <div class="field">
            <label for="email">E-posta</label>
            <input id="email" name="email" type="email" required value="<?= e($old['email']) ?>" autocomplete="email">
        </div>
        <div class="field">
            <label for="password">Şifre</label>
            <input id="password" name="password" type="password" required minlength="10" autocomplete="new-password">
            <p class="help">En az 10 karakter; en az bir harf ve bir rakam.</p>
        </div>
        <div class="field">
            <label for="password_confirm">Şifre (tekrar)</label>
            <input id="password_confirm" name="password_confirm" type="password" required minlength="10" autocomplete="new-password">
        </div>
        <button class="btn btn-primary btn-block" type="submit">Hesabı oluştur</button>
    </form>
<?php endif; ?>
