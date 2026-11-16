<p><a class="back" href="<?= e(admin_url('list', ['res' => 'gallery'])) ?>">← Fotoğraf Galerisi</a></p>
<form method="post" action="<?= e(admin_url('bulk')) ?>" enctype="multipart/form-data" class="panel form-grid">
    <?= csrf_field() ?>
    <div class="field">
        <label for="images">Fotoğraflar <span class="req">*</span></label>
        <input type="file" id="images" name="images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple required>
        <p class="help">Tek seferde en fazla 30 fotoğraf. Her biri en fazla <?= (int) config('app.upload_max_mb', 5) ?> MB (JPG, PNG, WEBP, GIF). Sunucunun toplam yükleme sınırı: <?= e(ini_get('post_max_size') ?: '-') ?>.</p>
    </div>
    <div class="field">
        <label for="title">Ortak başlık (isteğe bağlı)</label>
        <input type="text" id="title" name="title" maxlength="180" placeholder="ör. Bahar Şenliği 2026">
        <p class="help">Birden fazla fotoğrafta başlığın sonuna sıra numarası eklenir.</p>
    </div>
    <div class="field"><label class="switch"><input type="checkbox" name="is_published" value="1" checked><span>Hemen yayınla</span></label></div>
    <div class="form-actions"><button class="btn btn-primary" type="submit">Yükle</button></div>
</form>
