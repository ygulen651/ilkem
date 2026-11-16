const toggle = document.querySelector('.menu-toggle');
const navigation = document.querySelector('#navigation');
toggle.addEventListener('click', () => {
  const open = toggle.getAttribute('aria-expanded') !== 'true';
  toggle.setAttribute('aria-expanded', String(open));
  toggle.setAttribute('aria-label', open ? 'Menüyü kapat' : 'Menüyü aç');
  navigation.classList.toggle('open', open);
});
navigation.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
  navigation.classList.remove('open');
  toggle.setAttribute('aria-expanded', 'false');
  toggle.setAttribute('aria-label', 'Menüyü aç');
}));
const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (!entry.isIntersecting) return;
    navigation.querySelectorAll('a').forEach(link => link.classList.toggle('active', link.hash === '#' + entry.target.id));
  });
}, {rootMargin: '-15% 0px -55% 0px', threshold: 0});
document.querySelectorAll('main section[id]').forEach(section => observer.observe(section));
document.querySelectorAll('[data-filter]').forEach(button => button.addEventListener('click', () => {
  document.querySelectorAll('[data-filter]').forEach(item => {
    item.classList.toggle('selected', item === button);
    item.setAttribute('aria-pressed', String(item === button));
  });
  document.querySelectorAll('[data-kind]').forEach(row => { row.hidden = button.dataset.filter !== 'all' && row.dataset.kind !== button.dataset.filter; });
}));
const details = {
  education: ['Öğrenmenin yaşı yok.', 'Bilgi ve deneyimleri paylaşmaya, yeni beceriler kazanmaya ve birlikte gelişmeye alan açan bir proje örneği. Atölye içerikleri, katılım koşulları ve program bilgileri bu alanda yer alacak.'],
  nature: ['Yarına bir tohum.', 'Doğayla bağımızı güçlendirmek ve çevre farkındalığına katkı sunmak için hazırlanan örnek proje alanı. Projenin kapsamı ve etkinlik bilgileri daha sonra eklenecek.'],
  volunteer: ['Gönüllülük yolculuğuna birlikte başlayalım', 'Zamanınız, bilginiz ve emeğinizle bir arada olmanın değerini paylaşın. Bu bir örnek gönüllülük duyurusudur. Gerçek başvuru koşulları ve iletişim bilgileri eklendiğinde bu alandan paylaşılacak.'],
  workshop: ['Paylaşarak öğreniyor, birlikte gelişiyoruz', 'Birlikte öğrenmeye ve üretmeye odaklanan çalışmaların hikâyeleri burada yer alacak. Bu haber, sayfa düzenini göstermek için hazırlanmış örnek içeriktir.'],
  calendar: ['Yeni dönem, yeni buluşmalar', 'Atölye, söyleşi ve dernek buluşmalarına ilişkin güncel program burada paylaşılacak. Bu taslakta henüz kesinleşmiş bir etkinlik veya tarih bulunmuyor.']
};
const dialog = document.querySelector('#detail-dialog');
document.querySelectorAll('[data-detail]').forEach(button => button.addEventListener('click', () => {
  const [title, copy] = details[button.dataset.detail];
  document.querySelector('#detail-title').textContent = title;
  document.querySelector('#detail-copy').textContent = copy;
  dialog.showModal(); document.body.classList.add('dialog-open');
}));
document.querySelector('.dialog-close').addEventListener('click', () => dialog.close());
dialog.addEventListener('click', event => { if (event.target === dialog) {const rect = dialog.getBoundingClientRect(); if(event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) dialog.close();} });
dialog.addEventListener('close', () => document.body.classList.remove('dialog-open'));
document.querySelector('#detail-contact').addEventListener('click', () => dialog.close());
document.querySelector('#contact-form').addEventListener('submit', event => {
  event.preventDefault();
  document.querySelector('#form-status').textContent = 'Form kontrolü tamamlandı. Bu bir tasarım taslağıdır; mesajınız gönderilmedi ve bilgileriniz kaydedilmedi.';
});
document.querySelector('#year').textContent = new Date().getFullYear();
