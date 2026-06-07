(function () {
  'use strict';

  var KEY = 'autosoft_favs';

  function read() {
    try {
      var v = JSON.parse(localStorage.getItem(KEY) || '[]');
      return Array.isArray(v) ? v.filter(function (s) { return typeof s === 'string' && s; }) : [];
    } catch (e) { return []; }
  }

  function write(list) {
    try { localStorage.setItem(KEY, JSON.stringify(list)); } catch (e) {}
  }

  function has(slug) { return read().indexOf(slug) !== -1; }

  function toggle(slug) {
    var list = read();
    var i = list.indexOf(slug);
    var adding = i === -1;
    if (adding) { list.push(slug); } else { list.splice(i, 1); }
    write(list);
    try {
      if (window.fetch) {
        fetch((window.AUTOSOFT_BASE || '') + '/api/fav/' + encodeURIComponent(slug) + '/' + (adding ? 'add' : 'remove'),
          { method: 'POST', keepalive: true, credentials: 'same-origin' });
      }
    } catch (e) {}
    return adding;
  }

  function updateHeader() {
    var link = document.getElementById('header-fav');
    if (!link) return;
    var list = read();
    var badge = document.getElementById('fav-count');
    if (badge) { badge.textContent = String(list.length); }
    link.classList.toggle('has-favs', list.length > 0);
    var base = link.getAttribute('href').split('?')[0];
    link.setAttribute('href', list.length > 0 ? base + '?slugs=' + encodeURIComponent(list.join(',')) : base);
  }

  function paintButtons() {
    var buttons = document.querySelectorAll('[data-fav-slug]');
    for (var i = 0; i < buttons.length; i++) {
      var btn = buttons[i];
      var slug = btn.getAttribute('data-fav-slug');
      var active = has(slug);
      btn.classList.toggle('is-fav', active);
      var label = btn.querySelector('.fav-btn-lg-label');
      if (label) { label.textContent = active ? 'Guardado nos favoritos' : 'Guardar nos favoritos'; }
      btn.setAttribute('aria-pressed', active ? 'true' : 'false');
    }
  }

  document.addEventListener('click', function (ev) {
    var btn = ev.target.closest && ev.target.closest('[data-fav-slug]');
    if (!btn) return;
    ev.preventDefault();
    ev.stopPropagation();
    toggle(btn.getAttribute('data-fav-slug'));
    paintButtons();
    updateHeader();
  });

  function syncFavPage() {
    var page = document.getElementById('fav-page');
    if (!page) return;
    var hasSlugs = page.getAttribute('data-has-slugs') === '1';
    var list = read();

    if (!hasSlugs && list.length > 0) {
      var u = new URL(window.location.href);
      u.searchParams.set('slugs', list.join(','));
      window.location.replace(u.toString());
      return;
    }

    var empty = document.getElementById('fav-empty');
    var intro = document.getElementById('fav-intro');
    if (list.length === 0) {
      if (empty) { empty.hidden = false; }
      if (intro) { intro.textContent = 'Sem viaturas guardadas neste navegador.'; }
    } else if (intro) {
      intro.textContent = list.length + (list.length === 1 ? ' viatura guardada' : ' viaturas guardadas') + ' neste navegador.';
    }
  }

  function bindBurger() {
    var burger = document.getElementById('header-burger');
    var nav = document.getElementById('main-nav');
    if (!burger || !nav) return;
    burger.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.addEventListener('click', function (ev) {
      if (!nav.classList.contains('is-open')) return;
      if (ev.target.closest('#main-nav') || ev.target.closest('#header-burger')) return;
      nav.classList.remove('is-open');
      burger.setAttribute('aria-expanded', 'false');
    });
  }

  function initCursor() {
    var canFine = window.matchMedia && (
      window.matchMedia('(any-pointer: fine)').matches ||
      window.matchMedia('(pointer: fine)').matches
    );
    if (!canFine) return;
    var dot = document.getElementById('cursor-dot');
    var ring = document.getElementById('cursor-ring');
    if (!dot || !ring) return;

    var mx = -100, my = -100, rx = -100, ry = -100;
    var visible = false;
    var hoverSel = 'a, button, .btn, .fav-btn, .vcard, .header-fav, .header-burger, [role=button], label, summary, select';

    function activate() {
      if (visible) return;
      visible = true;
      document.documentElement.classList.add('has-custom-cursor');
    }

    window.addEventListener('mousemove', function (e) {
      mx = e.clientX; my = e.clientY;
      activate();
    }, { passive: true });

    window.addEventListener('mousedown', function () { document.documentElement.classList.add('cursor-down'); });
    window.addEventListener('mouseup',   function () { document.documentElement.classList.remove('cursor-down'); });
    document.addEventListener('mouseover', function (e) {
      if (e.target.closest && e.target.closest(hoverSel)) {
        document.documentElement.classList.add('cursor-hover');
      }
    });
    document.addEventListener('mouseout', function (e) {
      var to = e.relatedTarget;
      if (!to || !(to.closest && to.closest(hoverSel))) {
        document.documentElement.classList.remove('cursor-hover');
      }
    });
    document.documentElement.addEventListener('mouseleave', function () {
      dot.style.opacity = '0'; ring.style.opacity = '0';
    });
    document.documentElement.addEventListener('mouseenter', function () {
      dot.style.opacity = ''; ring.style.opacity = '';
    });

    (function tick() {
      rx += (mx - rx) * 0.22;
      ry += (my - ry) * 0.22;
      dot.style.transform  = 'translate3d(' + mx + 'px,' + my + 'px,0)';
      ring.style.transform = 'translate3d(' + rx + 'px,' + ry + 'px,0)';
      requestAnimationFrame(tick);
    })();
  }

  function bindGallery() {
    var mainBox = document.getElementById('gallery-main-media');
    var counter = document.getElementById('gallery-counter-current');
    var thumbs = document.querySelectorAll('.gallery-thumbs .thumb');
    if (!mainBox || thumbs.length === 0) return;
    Array.prototype.forEach.call(thumbs, function (btn) {
      btn.addEventListener('click', function () {
        var src = btn.getAttribute('data-gallery-src');
        var type = btn.getAttribute('data-gallery-type') || 'image';
        var idx = btn.getAttribute('data-gallery-index');
        if (!src) return;

        var counterHtml = counter ? '<span class="gallery-counter"><span id="gallery-counter-current">' + idx + '</span> / ' + thumbs.length + '</span>' : '';
        if (type === 'video') {
          mainBox.innerHTML = '<video src="' + src + '" controls autoplay playsinline></video>' + counterHtml;
        } else {
          mainBox.innerHTML = '<img src="' + src + '" alt="">' + counterHtml;
        }
        counter = document.getElementById('gallery-counter-current');

        Array.prototype.forEach.call(thumbs, function (t) { t.classList.remove('is-active'); });
        btn.classList.add('is-active');
      });
    });
  }

  function init() {
    updateHeader();
    paintButtons();
    syncFavPage();
    bindBurger();
    bindGallery();
    initCursor();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
