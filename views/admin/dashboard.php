<?php /* Dashboard — $counts, $recent */ ?>
<div class="page-head">
  <h1>Painel</h1>
  <a class="btn btn-primary" href="<?= url('/admin/viaturas/nova') ?>">+ Nova viatura</a>
</div>

<div class="stat-grid">
  <div class="stat-card"><span class="stat-n"><?= $counts['vehicles'] ?></span><span class="stat-l">Viaturas</span></div>
  <div class="stat-card"><span class="stat-n"><?= $counts['available'] ?></span><span class="stat-l">Disponíveis</span></div>
  <div class="stat-card"><span class="stat-n"><?= $counts['brands'] ?></span><span class="stat-l">Marcas</span></div>
  <div class="stat-card"><span class="stat-n"><?= $counts['categories'] ?></span><span class="stat-l">Categorias</span></div>
  <div class="stat-card <?= $counts['leads'] ? 'stat-alert' : '' ?>">
    <span class="stat-n"><?= $counts['leads'] ?></span><span class="stat-l">Contactos novos</span>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h2>Últimas viaturas</h2>
    <a class="btn btn-sm btn-outline" href="<?= url('/admin/viaturas') ?>">Ver todas</a>
  </div>
  <table class="data-table">
    <thead><tr><th>Viatura</th><th>Marca</th><th>Preço</th><th>Estado</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($recent as $v): ?>
      <tr>
        <td><strong><?= e($v['model']) ?></strong> <span class="muted"><?= e($v['version']) ?></span></td>
        <td><?= e($v['brand_name']) ?></td>
        <td class="mono"><?= e(kz($v['price'])) ?></td>
        <td><span class="pill pill-<?= e($v['status']) ?>"><?= e($v['status']) ?></span></td>
        <td class="row-actions"><a href="<?= url('/admin/viaturas/editar/'.$v['id']) ?>">Editar</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
