<section class="board-section">
    <div class="wrap">
        <div class="board">
            <div class="board-text">
                <p class="board-note"><?= e(setting('volunteer_eyebrow', 'Hafızamızı birlikte koruyalım')) ?></p>
                <h2 class="chalk-title"><?= e(setting('volunteer_title', 'Bir hatıra da sizden olsun.')) ?></h2>
                <p><?= e(setting('volunteer_text', 'Gönüllü olabilir, eski okul eşyalarınızı ve hatıralarınızı müzemize bağışlayabilirsiniz.')) ?></p>
            </div>
            <div class="board-actions">
                <a class="button" href="<?= e(link_url(setting('volunteer_button_url'), 'iletisim?konu=gonullu')) ?>"><?= e(setting('volunteer_button_text', 'Gönüllü Olmak İstiyorum')) ?></a>
                <a class="board-link" href="<?= e(url('iletisim?konu=bagis')) ?>">Eşya bağışı için yazın</a>
            </div>
            <span class="board-chalk" aria-hidden="true"></span>
            <span class="board-eraser" aria-hidden="true"></span>
        </div>
    </div>
</section>
