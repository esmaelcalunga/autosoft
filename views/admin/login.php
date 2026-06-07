<?php /* Login do painel — standalone. $error, $csrf, $title */ ?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Saira:wght@400;600;700;800&family=Archivo:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap">
<link rel="stylesheet" href="<?= asset('admin.css') ?>">
</head>
<body class="login-body">
<div class="login-card">
  <div class="login-brand">
    <svg viewBox="0 0 280 48" width="160" height="28">
      <polygon points="4,39 9.5,39 16,9 10.5,9" fill="#0E0F11"></polygon>
      <polygon points="13,39 18.5,39 25,9 19.5,9" fill="#0E0F11"></polygon>
      <polygon points="22,39 27.5,39 34,9 28.5,9" fill="#DA1E2F"></polygon>
      <text x="46" y="34" font-family="Saira, sans-serif" font-weight="800" font-size="27" letter-spacing="0.5">
        <tspan fill="#0E0F11">AUTO</tspan><tspan fill="#DA1E2F">SOFT</tspan>
      </text>
    </svg>
  </div>
  <h1>Painel administrativo</h1>
  <p class="login-sub">Inicie sessão para gerir o stock.</p>

  <?php if ($error): ?><div class="flash flash-error"><?= e($error) ?></div><?php endif; ?>

  <form method="post" action="<?= url('/admin/login') ?>" class="login-form">
    <?= $csrf ?>
    <label>Email<input type="email" name="email" value="admin@autosoft.ao" required autofocus></label>
    <label>Senha<input type="password" name="password" placeholder="••••••••" required></label>
    <button class="btn btn-primary btn-block" type="submit">Entrar</button>
  </form>
  <p class="login-hint">Credenciais por omissão: <code>admin@autosoft.ao</code> / <code>admin123</code></p>
  <a class="login-back" href="<?= url('/') ?>">← Voltar ao site</a>
</div>
</body>
</html>
