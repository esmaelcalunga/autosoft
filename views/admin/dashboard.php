<?php /* Dashboard — $counts, $recent, $topViewed, $topFavorited, $statusBreak, $brandBreak, $leadsSeries */
$statusLabels = ['disponivel' => 'Disponível', 'reservado' => 'Reservado', 'vendido' => 'Vendido'];
$statusColors = ['disponivel' => '#15924F', 'reservado' => '#E6A100', 'vendido' => '#646C76'];
$statusData = [];
foreach ($statusBreak as $r) {
  $statusData[] = ['label' => $statusLabels[$r['status']] ?? $r['status'], 'n' => (int)$r['n'], 'color' => $statusColors[$r['status']] ?? '#DA1E2F'];
}
$brandLabels = array_column($brandBreak, 'name');
$brandValues = array_map('intval', array_column($brandBreak, 'n'));
$leadsDays = []; $leadsValues = [];
$map = [];
foreach ($leadsSeries as $r) { $map[$r['d']] = (int)$r['n']; }
for ($i = 29; $i >= 0; $i--) {
  $d = date('Y-m-d', strtotime("-$i day"));
  $leadsDays[]   = date('d/m', strtotime($d));
  $leadsValues[] = $map[$d] ?? 0;
}
?>
<div class="page-head">
  <h1>Painel</h1>
  <a class="btn btn-primary" href="<?= url('/admin/viaturas/nova') ?>">+ Nova viatura</a>
</div>

<div class="stat-grid">
  <div class="stat-card"><span class="stat-n"><?= $counts['vehicles'] ?></span><span class="stat-l">Viaturas</span></div>
  <div class="stat-card"><span class="stat-n"><?= $counts['available'] ?></span><span class="stat-l">Disponíveis</span></div>
  <div class="stat-card"><span class="stat-n stat-accent"><?= number_format($counts['views'],0,',','.') ?></span><span class="stat-l">Visualizações</span></div>
  <div class="stat-card"><span class="stat-n stat-accent"><?= number_format($counts['favorites'],0,',','.') ?></span><span class="stat-l">Favoritos</span></div>
  <div class="stat-card <?= $counts['leads'] ? 'stat-alert' : '' ?>">
    <span class="stat-n"><?= $counts['leads'] ?>/<?= $counts['leads_total'] ?></span><span class="stat-l">Contactos (novos/total)</span>
  </div>
  <div class="stat-card"><span class="stat-n"><?= $counts['brands'] ?></span><span class="stat-l">Marcas</span></div>
</div>

<div class="dash-grid">
  <div class="panel">
    <div class="panel-head"><h2>Contactos · últimos 30 dias</h2></div>
    <div class="panel-body"><canvas id="chart-leads" height="120"></canvas></div>
  </div>
  <div class="panel">
    <div class="panel-head"><h2>Estado do stock</h2></div>
    <div class="panel-body chart-with-legend">
      <canvas id="chart-status" height="180"></canvas>
      <ul class="chart-legend">
        <?php foreach ($statusData as $s): ?>
          <li><i style="background:<?= e($s['color']) ?>"></i><?= e($s['label']) ?> <strong><?= $s['n'] ?></strong></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h2>Viaturas por marca</h2></div>
  <div class="panel-body"><canvas id="chart-brands" height="80"></canvas></div>
</div>

<div class="dash-grid">
  <div class="panel">
    <div class="panel-head"><h2>★ Mais visualizadas</h2><a class="btn btn-sm btn-outline" href="<?= url('/admin/viaturas?sort=views&dir=desc') ?>">Ver todas</a></div>
    <table class="data-table">
      <thead><tr><th>Viatura</th><th class="num">Vistas</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($topViewed as $v): ?>
          <tr>
            <td><strong><?= e($v['brand_name'].' '.$v['model']) ?></strong><div class="muted"><?= e($v['version']) ?></div></td>
            <td class="mono num"><?= (int)($v['views'] ?? 0) ?></td>
            <td class="row-actions"><a href="<?= url('/admin/viaturas/editar/'.$v['id']) ?>">Editar</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="panel">
    <div class="panel-head"><h2>❤ Mais favoritadas</h2><a class="btn btn-sm btn-outline" href="<?= url('/admin/viaturas?sort=favorites&dir=desc') ?>">Ver todas</a></div>
    <table class="data-table">
      <thead><tr><th>Viatura</th><th class="num">Favoritos</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($topFavorited as $v): ?>
          <tr>
            <td><strong><?= e($v['brand_name'].' '.$v['model']) ?></strong><div class="muted"><?= e($v['version']) ?></div></td>
            <td class="mono num"><?= (int)($v['favorites'] ?? 0) ?></td>
            <td class="row-actions"><a href="<?= url('/admin/viaturas/editar/'.$v['id']) ?>">Editar</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h2>📞 Viaturas mais contactadas</h2><a class="btn btn-sm btn-outline" href="<?= url('/admin/viaturas?sort=contacts&dir=desc') ?>">Ver todas</a></div>
  <table class="data-table">
    <thead><tr><th>Viatura</th><th>Marca</th><th class="num">Contactos</th><th class="num">Vistas</th><th class="num">Favs</th><th></th></tr></thead>
    <tbody>
      <?php if (!$topContacted): ?>
        <tr><td colspan="6" class="empty-row">Ainda nenhuma viatura recebeu contactos.</td></tr>
      <?php endif; ?>
      <?php foreach ($topContacted as $v): ?>
        <tr>
          <td><strong><?= e($v['model']) ?></strong><div class="muted"><?= e($v['version']) ?></div></td>
          <td><?= e($v['brand_name']) ?></td>
          <td class="mono num"><strong><?= (int)($v['contacts'] ?? 0) ?></strong></td>
          <td class="mono num"><?= (int)($v['views'] ?? 0) ?></td>
          <td class="mono num"><?= (int)($v['favorites'] ?? 0) ?></td>
          <td class="row-actions"><a href="<?= url('/admin/viaturas/editar/'.$v['id']) ?>">Editar</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="panel">
  <div class="panel-head"><h2>Últimas viaturas</h2><a class="btn btn-sm btn-outline" href="<?= url('/admin/viaturas') ?>">Ver todas</a></div>
  <table class="data-table">
    <thead><tr><th>Viatura</th><th>Marca</th><th>Preço</th><th class="num">Vistas</th><th class="num">Favs</th><th class="num">Contactos</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($recent as $v): ?>
      <tr>
        <td><strong><?= e($v['model']) ?></strong> <span class="muted"><?= e($v['version']) ?></span></td>
        <td><?= e($v['brand_name']) ?></td>
        <td class="mono"><?= e(kz($v['price'])) ?></td>
        <td class="mono num"><?= (int)($v['views'] ?? 0) ?></td>
        <td class="mono num"><?= (int)($v['favorites'] ?? 0) ?></td>
        <td class="mono num"><?= (int)($v['contacts'] ?? 0) ?></td>
        <td><span class="pill pill-<?= e($v['status']) ?>"><?= e($v['status']) ?></span></td>
        <td class="row-actions"><a href="<?= url('/admin/viaturas/editar/'.$v['id']) ?>">Editar</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
window.AUTOSOFT_DASH = {
  status: <?= json_encode($statusData) ?>,
  brandLabels: <?= json_encode($brandLabels) ?>,
  brandValues: <?= json_encode($brandValues) ?>,
  leadsDays: <?= json_encode($leadsDays) ?>,
  leadsValues: <?= json_encode($leadsValues) ?>
};
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?= asset('dashboard.js') ?>"></script>
