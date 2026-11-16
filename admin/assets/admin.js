/* İlkem Derneği - yönetim paneli etkileşimleri */
(function () {
  'use strict';
  // Silme onayı
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.getAttribute('data-confirm'))) e.preventDefault();
    });
  });

  // Mobil yan menü
  var toggle = document.querySelector('.side-toggle');
  var setOpen = function (open) {
    document.body.classList.toggle('sidebar-open', open);
    if (toggle) { toggle.setAttribute('aria-expanded', String(open)); toggle.setAttribute('aria-label', open ? 'Menüyü kapat' : 'Menüyü aç'); }
  };
  if (toggle) toggle.addEventListener('click', function () { setOpen(!document.body.classList.contains('sidebar-open')); });
  document.querySelectorAll('[data-close-sidebar]').forEach(function (el) { el.addEventListener('click', function () { setOpen(false); }); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });

  // Görsel önizleme
  document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
    input.addEventListener('change', function () {
      var old = input.parentNode.querySelector('.image-preview');
      if (old) old.remove();
      var file = input.files && input.files[0];
      if (!file || !/^image\//.test(file.type)) return;
      var img = document.createElement('img');
      img.className = 'image-preview';
      img.alt = 'Önizleme';
      img.src = URL.createObjectURL(file);
      input.insertAdjacentElement('afterend', img);
    });
  });

  // Kaydedilmemiş değişiklik uyarısı
  document.querySelectorAll('form[data-dirty-check]').forEach(function (form) {
    var dirty = false;
    form.addEventListener('input', function () { dirty = true; });
    form.addEventListener('change', function () { dirty = true; });
    form.addEventListener('submit', function () { dirty = false; });
    window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
  });
})();
