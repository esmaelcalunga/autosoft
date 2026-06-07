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
    if (i === -1) { list.push(slug); } else { list.splice(i, 1); }
    write(list);
    return i === -1;
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

  function init() {
    updateHeader();
    paintButtons();
    syncFavPage();
    bindBurger();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
