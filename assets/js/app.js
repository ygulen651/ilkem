/* İlkem Derneği - ziyaretçi arayüzü etkileşimleri */
(function () {
  'use strict';
  document.documentElement.classList.remove('no-js');
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Mobil menü */
  var toggle = document.querySelector('.menu-toggle');
  var nav = document.getElementById('navigation');
  function setMenu(open) {
    if (!toggle || !nav) return;
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', open ? 'Menüyü kapat' : 'Menüyü aç');
    nav.classList.toggle('open', open);
    document.body.classList.toggle('no-scroll', open);
  }
  if (toggle && nav) {
    toggle.addEventListener('click', function () { setMenu(toggle.getAttribute('aria-expanded') !== 'true'); });
    nav.addEventListener('click', function (e) { if (e.target.closest('a')) setMenu(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setMenu(false); });
    window.addEventListener('resize', function () { if (window.innerWidth > 1080) setMenu(false); });
  }

  /* Kaydırınca küçülen üst menü */
  var header = document.getElementById('site-header');
  if (header) {
    var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 40); };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* Açılış alanı slaytları: ilerleme çubuğu dolunca sonraki slayta geçer */
  var slider = document.querySelector('[data-slider]');
  if (slider) {
    var slides = slider.querySelectorAll('.hero-slide');
    var dots = slider.querySelectorAll('.hero-dot');
    var current = 0;
    var show = function (i) {
      current = (i + slides.length) % slides.length;
      slides.forEach(function (s, n) {
        s.classList.toggle('is-active', n === current);
        if (n === current) s.removeAttribute('aria-hidden'); else s.setAttribute('aria-hidden', 'true');
      });
      dots.forEach(function (d, n) { d.classList.toggle('is-active', n === current); d.setAttribute('aria-current', n === current ? 'true' : 'false'); });
    };
    dots.forEach(function (d) {
      d.addEventListener('click', function () { show(parseInt(d.dataset.slide, 10)); });
      var bar = d.querySelector('span');
      if (bar) bar.addEventListener('animationend', function () { if (d.classList.contains('is-active')) show(current + 1); });
    });
    var pause = function () { slider.classList.add('is-paused'); };
    var resume = function () { slider.classList.remove('is-paused'); };
    slider.addEventListener('mouseenter', pause);
    slider.addEventListener('mouseleave', resume);
    slider.addEventListener('focusin', pause);
    slider.addEventListener('focusout', resume);
  }

  /* Duyuru şeridi */
  var ticker = document.querySelector('[data-ticker]');
  if (ticker && !reduceMotion) {
    var items = ticker.querySelectorAll('li');
    if (items.length > 1) {
      var t = 0;
      setInterval(function () {
        var prev = items[t];
        prev.classList.remove('is-active');
        prev.classList.add('is-leaving');
        setTimeout(function () { prev.classList.remove('is-leaving'); }, 600);
        t = (t + 1) % items.length;
        items[t].classList.add('is-active');
      }, 5000);
    }
  }

  /* YouTube videosunu tıklanınca yükle (gizlilik dostu) */
  document.querySelectorAll('[data-youtube]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = btn.getAttribute('data-youtube');
      if (!/^[A-Za-z0-9_-]{11}$/.test(id)) return;
      var iframe = document.createElement('iframe');
      iframe.src = 'https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1&rel=0';
      iframe.title = 'Tanıtım videosu';
      iframe.allow = 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen';
      iframe.allowFullscreen = true;
      btn.replaceWith(iframe);
    });
  });

  /* Siteye yüklenmiş MP4/WEBM tanıtım videosu: tıklanınca oynatıcıyı yükle */
  document.querySelectorAll('[data-video]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var src = btn.getAttribute('data-video');
      if (!src) return;
      var video = document.createElement('video');
      video.src = src;
      video.controls = true;
      video.autoplay = true;
      video.playsInline = true;
      video.setAttribute('playsinline', '');
      var poster = btn.querySelector('img');
      if (poster) video.poster = poster.src;
      btn.replaceWith(video);
      video.play().catch(function () {});
    });
  });

  /* Galeri ışık kutusu */
  var box = document.getElementById('lightbox');
  if (box) {
    var img = box.querySelector('img');
    var cap = box.querySelector('figcaption');
    var list = [], index = 0, lastFocus = null;
    var open = function (links, i) {
      list = links; index = i; lastFocus = document.activeElement;
      render(); box.hidden = false; document.body.classList.add('no-scroll');
      box.querySelector('.lightbox-close').focus();
    };
    var close = function () { box.hidden = true; document.body.classList.remove('no-scroll'); if (lastFocus) lastFocus.focus(); };
    var render = function () {
      var a = list[index];
      img.src = a.getAttribute('href');
      img.alt = a.dataset.caption || '';
      cap.textContent = a.dataset.caption || '';
      var multi = list.length > 1;
      box.querySelector('.lightbox-prev').hidden = !multi;
      box.querySelector('.lightbox-next').hidden = !multi;
    };
    var step = function (d) { index = (index + d + list.length) % list.length; render(); };
    document.querySelectorAll('[data-gallery]').forEach(function (g) {
      var links = Array.prototype.slice.call(g.querySelectorAll('a.gallery-item'));
      links.forEach(function (a, i) {
        a.addEventListener('click', function (e) { e.preventDefault(); open(links, i); });
      });
    });
    box.querySelector('.lightbox-close').addEventListener('click', close);
    box.querySelector('.lightbox-prev').addEventListener('click', function () { step(-1); });
    box.querySelector('.lightbox-next').addEventListener('click', function () { step(1); });
    box.addEventListener('click', function (e) { if (e.target === box) close(); });
    document.addEventListener('keydown', function (e) {
      if (box.hidden) return;
      if (e.key === 'Escape') close();
      if (e.key === 'ArrowLeft') step(-1);
      if (e.key === 'ArrowRight') step(1);
    });
  }

  /* Tebeşir çizgileri ve kara tahta yazısı görünür olunca çizilir */
  var drawTargets = document.querySelectorAll('.scribble, .chalk-title');
  if ('IntersectionObserver' in window && !reduceMotion) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-drawn'); io.unobserve(en.target); } });
    }, { rootMargin: '0px 0px -60px 0px' });
    drawTargets.forEach(function (el) { io.observe(el); });
  } else {
    drawTargets.forEach(function (el) { el.classList.add('is-drawn'); });
  }
})();
