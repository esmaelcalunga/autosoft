<?php /* Layout público — recebe $content e $title */ ?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'AutoSOFT') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Saira:wght@300;400;500;600;700;800&family=Archivo:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap">
<link rel="stylesheet" href="<?= asset('site.css') ?>">
<script src="<?= asset('favorites.js') ?>" defer></script>
</head>
<body>
<div class="cursor-dot" id="cursor-dot" aria-hidden="true"></div>
<div class="cursor-ring" id="cursor-ring" aria-hidden="true"><i class="cursor-ring-inner"></i></div>
<?php
$navActive = $navActive ?? '';
$links = [
    ['home',    'Início',         url('/')],
    ['catalog', 'Stock',          url('/estoque')],
    ['sell',    'Vender viatura', url('/vender')],
    ['about',   'A AutoSOFT',     url('/sobre')],
];
?>
<header class="site-header">
  <div class="container header-inner">
    <a href="<?= url('/') ?>" class="logo" aria-label="AutoSOFT">
      <svg viewBox="0 0 280 48" width="150" height="26" role="img">
        <polygon points="4,39 9.5,39 16,9 10.5,9" fill="#0E0F11"></polygon>
        <polygon points="13,39 18.5,39 25,9 19.5,9" fill="#0E0F11"></polygon>
        <polygon points="22,39 27.5,39 34,9 28.5,9" fill="#DA1E2F"></polygon>
        <text x="46" y="34" font-family="Saira, sans-serif" font-weight="800" font-size="27" letter-spacing="0.5">
          <tspan fill="#0E0F11">AUTO</tspan><tspan fill="#DA1E2F">SOFT</tspan>
        </text>
      </svg>
    </a>
    <nav class="main-nav" id="main-nav">
      <?php foreach ($links as [$id, $label, $href]): ?>
        <a href="<?= $href ?>" class="<?= $navActive === $id ? 'active' : '' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <form class="header-search" action="<?= url('/estoque') ?>" method="get" role="search">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
      <input type="search" name="q" placeholder="Pesquisar viatura" value="<?= e(q('q')) ?>">
      <button type="submit" class="visually-hidden" tabindex="-1" aria-hidden="true">Pesquisar</button>
    </form>
    <a class="header-fav" href="<?= url('/favoritos') ?>" id="header-fav" aria-label="Favoritos">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      <span class="fav-badge" id="fav-count">0</span>
    </a>
    <button type="button" class="header-burger" id="header-burger" aria-label="Menu" aria-expanded="false" aria-controls="main-nav">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<main>
<?php
foreach (get_flashes() as $f):
?>
  <div class="container"><div class="flash flash-<?= e($f['type']) ?>"><?= e($f['msg']) ?></div></div>
<?php endforeach; ?>
<?= $content ?>
</main>

<footer class="site-footer">
  <div class="container footer-grid">
    <div class="footer-brand">
      <svg viewBox="0 0 280 48" width="150" height="26" role="img">
        <polygon points="4,39 9.5,39 16,9 10.5,9" fill="#FFFFFF"></polygon>
        <polygon points="13,39 18.5,39 25,9 19.5,9" fill="#FFFFFF"></polygon>
        <polygon points="22,39 27.5,39 34,9 28.5,9" fill="#DA1E2F"></polygon>
        <text x="46" y="34" font-family="Saira, sans-serif" font-weight="800" font-size="27" letter-spacing="0.5">
          <tspan fill="#FFFFFF">AUTO</tspan><tspan fill="#DA1E2F">SOFT</tspan>
        </text>
      </svg>
      <p>Stand premium de seminovas e 0 km. Proveniência verificada, inspeção técnica e garantia real.</p>
    </div>
    <div class="footer-col">
      <h4>Navegar</h4>
      <a href="<?= url('/estoque') ?>">Todo o stock</a>
      <a href="<?= url('/vender') ?>">Vender viatura</a>
      <a href="<?= url('/sobre') ?>">A AutoSOFT</a>
    </div>
    <div class="footer-col">
      <h4>Categorias</h4>
      <?php foreach (array_slice(all_categories(), 0, 5) as $c): ?>
        <a href="<?= url('/categoria/' . $c['slug']) ?>"><?= e($c['name']) ?></a>
      <?php endforeach; ?>
    </div>
    <div class="footer-col">
      <h4>Contacto</h4>
      <a href="tel:+244900000000">+244 900 000 000</a>
      <a href="mailto:geral@autosoft.ao">geral@autosoft.ao</a>
      <span class="footer-muted">Luanda · Talatona · Benguela</span>
    </div>
  </div>
  <div class="container footer-bottom">
    <span>© <?= date('Y') ?> AutoSOFT. Todos os direitos reservados.</span>
    <a href="<?= url('/admin') ?>">Painel administrativo</a>
  </div>
</footer>
</body>
</html>
