<?php
$siteName = site_name();
$logo = setting('logo');
$brand = brand_parts();
$socials = social_links();
$email = setting('contact_email');
$phone = setting('contact_phone');
$address = setting('contact_address');
?>
</main>
<footer class="site-footer">
    <div class="wrap footer-grid">
        <div class="footer-about">
            <a class="brand" href="<?= e(url()) ?>">
                <img class="brand-logo" src="<?= e($logo !== '' && has_media($logo) ? media_url($logo) : url('assets/img/logo.svg')) ?>" alt="" height="48">
                <span class="brand-text<?= $brand[2] ? ' brand-long' : '' ?>"><?= e($brand[0]) ?><?php if ($brand[1] !== ''): ?><small><?= e($brand[1]) ?></small><?php endif; ?></span>
            </a>
            <p><?= e(setting('footer_text', 'İlk Eğitim Müzesi Derneği; eğitim mirasımızı yaşatmak için çalışır.')) ?></p>
            <?php if ($socials !== []): ?>
                <ul class="social-list" aria-label="Sosyal medya">
                    <?php foreach ($socials as $key => $href): ?>
                        <li><a href="<?= e($href) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e(social_label($key)) ?>"><?= icon($key) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div>
            <h2 class="footer-title">Kurumsal</h2>
            <ul class="footer-links">
                <li><a href="<?= e(url('hakkimizda')) ?>">Hakkımızda</a></li>
                <li><a href="<?= e(url('faaliyetlerimiz')) ?>">Faaliyetlerimiz</a></li>
                <li><a href="<?= e(url('projeler')) ?>">Projeler</a></li>
                <li><a href="<?= e(url('galeri')) ?>">Fotoğraf Galerisi</a></li>
            </ul>
        </div>
        <div>
            <h2 class="footer-title">Güncel</h2>
            <ul class="footer-links">
                <li><a href="<?= e(url('haberler?tur=haber')) ?>">Haberler</a></li>
                <li><a href="<?= e(url('haberler?tur=duyuru')) ?>">Duyurular</a></li>
                <li><a href="<?= e(url('iletisim?konu=gonullu')) ?>">Gönüllü Ol</a></li>
                <li><a href="<?= e(url('iletisim')) ?>">İletişim</a></li>
            </ul>
        </div>
        <div>
            <h2 class="footer-title">İletişim</h2>
            <ul class="footer-contact">
                <?php if ($address !== ''): ?><li><?= icon('map-pin') ?><span><?= nl2br(e($address)) ?></span></li><?php endif; ?>
                <?php if ($phone !== ''): ?><li><?= icon('phone') ?><a href="<?= e(tel_href($phone)) ?>"><?= e($phone) ?></a></li><?php endif; ?>
                <?php if ($email !== ''): ?><li><?= icon('mail') ?><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></li><?php endif; ?>
                <?php if ($address === '' && $phone === '' && $email === ''): ?><li class="muted">İletişim bilgileri yakında eklenecek.</li><?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="wrap footer-bottom">
        <span>© <?= date('Y') ?> <?= e($siteName) ?><?= site_full_name() !== '' ? ' (' . e(site_full_name()) . ')' : '' ?>. Tüm hakları saklıdır.</span>
        <a href="#site-header" class="to-top">Başa dön</a>
    </div>
</footer>
<div class="lightbox" id="lightbox" hidden>
    <button type="button" class="lightbox-close" aria-label="Kapat"><?= icon('x') ?></button>
    <button type="button" class="lightbox-nav lightbox-prev" aria-label="Önceki"><?= icon('arrow-left') ?></button>
    <figure><img alt=""><figcaption></figcaption></figure>
    <button type="button" class="lightbox-nav lightbox-next" aria-label="Sonraki"><?= icon('arrow-right') ?></button>
</div>
</body>
</html>
