<?php /** @var array $activities */
partial('page-banner', [
    'title' => setting('about_title', 'Hakkımızda'),
    'eyebrow' => 'Bizi tanıyın',
    'lead' => setting('about_lead', 'Ortak değerlerle bir araya gelen, paylaşan ve birlikte üreten bir topluluk.'),
    'crumbs' => [setting('about_title', 'Hakkımızda') => null],
    'image' => setting('about_banner_image'),
]);
$values = array_values(array_filter(array_map('trim', preg_split('/\R/u', setting('about_values')) ?: []), static fn ($v) => $v !== ''));
?>
<section class="section wrap about about-page">
    <figure class="photo-print">
        <span class="tape" aria-hidden="true"></span>
        <img src="<?= e(media_url(setting('about_image', 'assets/img/together.jpg'), 'assets/img/together.jpg')) ?>" alt="" loading="lazy">
        <figcaption><?= e(setting('about_note', 'Bir kara tahta, bir kuşağın hikâyesidir.')) ?></figcaption>
    </figure>
    <div class="about-copy prose">
        <h2 class="scribble"><?= e(setting('about_heading', 'Okul hatıraları, ortak hafızamız.')) ?></h2>
        <?php $content = render_text(setting('about_content')); ?>
        <?= $content !== '' ? $content : '<p>Hakkımızda içeriği yakında eklenecek.</p>' ?>
    </div>
</section>

<?php if (setting('about_mission') !== '' || setting('about_vision') !== ''): ?>
<section class="section-band paper-band">
    <div class="wrap section mission-grid">
        <?php if (setting('about_mission') !== ''): ?>
            <article class="mission-card">
                <span class="activity-icon"><?= icon('lightbulb') ?></span>
                <h2>Misyonumuz</h2>
                <?= render_text(setting('about_mission')) ?>
            </article>
        <?php endif; ?>
        <?php if (setting('about_vision') !== ''): ?>
            <article class="mission-card">
                <span class="activity-icon"><?= icon('globe') ?></span>
                <h2>Vizyonumuz</h2>
                <?= render_text(setting('about_vision')) ?>
            </article>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($values !== []): ?>
<section class="section wrap">
    <div class="section-heading"><h2 class="scribble">Değerlerimiz</h2></div>
    <ul class="values-list">
        <?php foreach ($values as $n => $value): ?>
            <li><?= icon('star') ?><?= e($value) ?></li>
        <?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>

<?php if ($activities !== []): ?>
<section class="section-band paper-band">
    <div class="wrap section">
        <div class="section-heading">
            <h2 class="scribble">Faaliyet alanlarımız</h2>
            <a class="text-link" href="<?= e(url('faaliyetlerimiz')) ?>">Tüm faaliyetler</a>
        </div>
        <div class="activity-grid">
            <?php foreach ($activities as $activity) { partial('activity-card', ['activity' => $activity]); } ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php partial('volunteer'); ?>
