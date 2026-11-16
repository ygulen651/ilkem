<?php
/** @var array $errors @var array $old */
partial('page-banner', [
    'title' => 'İletişim', 'eyebrow' => 'Bir merhaba ile başlar',
    'lead' => 'Derneğimiz, çalışmalarımız ve gönüllülük hakkında bizimle iletişime geçebilirsiniz.',
    'crumbs' => ['İletişim' => null],
]);
$email = setting('contact_email');
$phone = setting('contact_phone');
$address = setting('contact_address');
$hours = setting('contact_hours');
$map = safe_map_embed(setting('contact_map_embed'));
$socials = social_links();
$err = static fn (string $k): string => isset($errors[$k]) ? '<p class="field-error" id="err-' . $k . '">' . e($errors[$k]) . '</p>' : '';
$aria = static fn (string $k): string => isset($errors[$k]) ? ' aria-invalid="true" aria-describedby="err-' . $k . '"' : '';
$val = static fn (string $k): string => e($old[$k] ?? '');
?>
<section class="section wrap contact">
    <div class="contact-info">
        <h2 class="scribble">Tanışalım</h2>
        <p><?= e(setting('contact_intro', 'Sorularınız, önerileriniz veya gönüllülük başvurunuz için formu doldurabilir ya da doğrudan bize ulaşabilirsiniz.')) ?></p>
        <ul class="contact-list">
            <?php if ($address !== ''): ?><li><?= icon('map-pin') ?><div><strong>Adres</strong><span><?= nl2br(e($address)) ?></span></div></li><?php endif; ?>
            <?php if ($phone !== ''): ?><li><?= icon('phone') ?><div><strong>Telefon</strong><a href="<?= e(tel_href($phone)) ?>"><?= e($phone) ?></a></div></li><?php endif; ?>
            <?php if ($email !== ''): ?><li><?= icon('mail') ?><div><strong>E-posta</strong><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></div></li><?php endif; ?>
            <?php if ($hours !== ''): ?><li><?= icon('clock') ?><div><strong>Çalışma saatleri</strong><span><?= nl2br(e($hours)) ?></span></div></li><?php endif; ?>
        </ul>
        <?php if ($address === '' && $phone === '' && $email === ''): ?>
            <div class="contact-placeholder"><strong>İletişim bilgileri</strong><p>Adres, telefon ve e-posta bilgileri yakında eklenecek. Bu süre içinde formu kullanabilirsiniz.</p></div>
        <?php endif; ?>
        <?php if ($socials !== []): ?>
            <ul class="social-list social-list-dark" aria-label="Sosyal medya">
                <?php foreach ($socials as $key => $href): ?>
                    <li><a href="<?= e($href) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e(social_label($key)) ?>"><?= icon($key) ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <form class="contact-form" id="form" method="post" action="<?= e(url('iletisim')) ?>#form" novalidate>
        <?= csrf_field() ?>
        <div class="form-heading"><h3>Bize bir mesaj bırakın</h3><span>* zorunlu alanlar</span></div>
        <?php if (isset($errors['form'])): ?><div class="form-alert" role="alert"><?= e($errors['form']) ?></div><?php elseif ($errors !== []): ?><div class="form-alert" role="alert">Lütfen işaretli alanları kontrol edin.</div><?php endif; ?>

        <div class="hp-field" aria-hidden="true">
            <label for="website">Web siteniz</label>
            <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
        </div>

        <div class="form-row">
            <div>
                <label for="name">Adınız soyadınız *</label>
                <input id="name" name="name" autocomplete="name" maxlength="100" required value="<?= $val('name') ?>"<?= $aria('name') ?>>
                <?= $err('name') ?>
            </div>
            <div>
                <label for="email">E-posta adresiniz *</label>
                <input id="email" name="email" type="email" autocomplete="email" maxlength="150" required value="<?= $val('email') ?>"<?= $aria('email') ?>>
                <?= $err('email') ?>
            </div>
        </div>
        <div class="form-row">
            <div>
                <label for="phone">Telefon</label>
                <input id="phone" name="phone" type="tel" autocomplete="tel" maxlength="20" value="<?= $val('phone') ?>"<?= $aria('phone') ?>>
                <?= $err('phone') ?>
            </div>
            <div>
                <label for="subject">Konu *</label>
                <select id="subject" name="subject" required<?= $aria('subject') ?>>
                    <option value="">Seçiniz</option>
                    <?php foreach (CONTACT_SUBJECTS as $key => $label): ?>
                        <option value="<?= e($key) ?>"<?= ($old['subject'] ?? '') === $key ? ' selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= $err('subject') ?>
            </div>
        </div>
        <label for="message">Mesajınız *</label>
        <textarea id="message" name="message" rows="6" maxlength="5000" required<?= $aria('message') ?>><?= $val('message') ?></textarea>
        <?= $err('message') ?>

        <label class="checkbox">
            <input type="checkbox" name="consent" value="1"<?= !empty($old) && empty($errors['consent']) ? ' checked' : '' ?><?= $aria('consent') ?>>
            <span>Gönderdiğim bilgilerin yalnızca bu talebe yanıt verilmesi amacıyla işlenmesini kabul ediyorum. *</span>
        </label>
        <?= $err('consent') ?>

        <div class="form-bottom">
            <p>Mesajınız güvenli şekilde iletilir.</p>
            <button class="button" type="submit">Mesajı Gönder</button>
        </div>
    </form>
</section>

<?php if ($map): ?>
<section class="map-section">
    <iframe src="<?= e($map) ?>" title="Harita" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
</section>
<?php endif; ?>
