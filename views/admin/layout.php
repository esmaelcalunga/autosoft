<?php /* Layout do painel — $content, $title, $active */
$admin = current_admin();
$active = $active ?? '';
$nav = [
  ['dashboard',  'Painel',     url('/admin')],
  ['viaturas',   'Viaturas',   url('/admin/viaturas')],
  ['marcas',     'Marcas',     url('/admin/marcas')],
  ['categorias', 'Categorias', url('/admin/categorias')],
  ['leads',      'Contactos',  url('/admin/leads')],
];
?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'AutoSOFT Admin') ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Saira:wght@400;600;700;800&family=Archivo:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap">
<link rel="stylesheet" href="<?= asset('admin.css') ?>">
<script src="<?= asset('admin.js') ?>" defer></script>
</head>
<body class="admin-body">
<aside class="admin-sidebar">
  <a href="<?= url('/admin') ?>" class="admin-logo">
    <svg viewBox="0 0 280 48" width="140" height="24">
      <polygon points="4,39 9.5,39 16,9 10.5,9" fill="#fff"></polygon>
      <polygon points="13,39 18.5,39 25,9 19.5,9" fill="#fff"></polygon>
      <polygon points="22,39 27.5,39 34,9 28.5,9" fill="#DA1E2F"></polygon>
      <text x="46" y="34" font-family="Saira, sans-serif" font-weight="800" font-size="27" letter-spacing="0.5">
        <tspan fill="#fff">AUTO</tspan><tspan fill="#DA1E2F">SOFT</tspan>
      </text>
    </svg>
  </a>
  <nav class="admin-nav">
    <?php foreach ($nav as [$id, $label, $href]): ?>
      <a href="<?= $href ?>" class="<?= $active === $id ? 'active' : '' ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="admin-side-foot">
    <a href="<?= url('/') ?>" target="_blank">Ver site ↗</a>
  </div>
</aside>

<div class="admin-main">
  <header class="admin-topbar">
    <div class="admin-crumb"><?= e($title ?? '') ?></div>
    <div class="admin-user">
      <span><?= e($admin['name'] ?? '') ?></span>
      <a class="btn btn-sm btn-outline" href="<?= url('/admin/logout') ?>">Sair</a>
    </div>
  </header>

  <div class="admin-content">
    <?php foreach (get_flashes() as $f): ?>
      <div class="flash flash-<?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
  </div>
</div>
</body>
</html>
