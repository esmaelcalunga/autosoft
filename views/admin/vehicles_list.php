<?php /* Lista de viaturas — $vehicles, $search, $sort */ ?>
<div class="page-head">
  <h1>Viaturas <span class="count-badge"><?= count($vehicles) ?></span></h1>
  <a class="btn btn-primary" href="<?= url('/admin/viaturas/nova') ?>">+ Nova viatura</a>
</div>

<form class="admin-toolbar" method="get" action="<?= url('/admin/viaturas') ?>">
  <div class="admin-search">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
    <input type="search" name="q" value="<?= e($search) ?>" placeholder="Pesquisar modelo, versão ou marca...">
  </div>
  <label class="admin-sort">Ordenar
    <select name="ordenar" onchange="this.form.submit()">
      <option value="recent"     <?= $sort==='recent'?'selected':'' ?>>Mais recentes</option>
      <option value="price-desc" <?= $sort==='price-desc'?'selected':'' ?>>Maior preço</option>
      <option value="price-asc"  <?= $sort==='price-asc'?'selected':'' ?>>Menor preço</option>
      <option value="views"      <?= $sort==='views'?'selected':'' ?>>Mais vistas</option>
      <option value="favorites"  <?= $sort==='favorites'?'selected':'' ?>>Mais favoritadas</option>
    </select>
  </label>
  <button class="btn btn-outline" type="submit">Aplicar</button>
  <?php if ($search || $sort !== 'recent'): ?>
    <a class="btn btn-ghost" href="<?= url('/admin/viaturas') ?>">Limpar</a>
  <?php endif; ?>
</form>

<div class="panel">
  <div class="table-scroll">
  <table class="data-table">
    <thead>
      <tr><th>Viatura</th><th>Marca</th><th>Categoria</th><th>Ano</th><th>Preço</th><th>Estado</th><th class="num">👁</th><th class="num">❤</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (!$vehicles): ?>
        <tr><td colspan="9" class="empty-row">
          <?= $search ? 'Sem resultados para "'.e($search).'".' : 'Ainda não há viaturas.' ?>
          <?php if (!$search): ?> <a href="<?= url('/admin/viaturas/nova') ?>">Adicione a primeira</a>.<?php endif; ?>
        </td></tr>
      <?php endif; ?>
      <?php foreach ($vehicles as $v): ?>
      <tr>
        <td>
          <div class="cell-title"><strong><?= e($v['model']) ?></strong> <?= $v['featured'] ? '<span class="pill pill-feat">destaque</span>' : '' ?></div>
          <div class="muted"><?= e($v['version']) ?></div>
        </td>
        <td><?= e($v['brand_name']) ?></td>
        <td><?= e($v['category_name'] ?: '—') ?></td>
        <td class="mono"><?= e(substr((string)$v['year'],0,4)) ?></td>
        <td class="mono"><?= e(kz($v['price'])) ?></td>
        <td><span class="pill pill-<?= e($v['status']) ?>"><?= e($v['status']) ?></span></td>
        <td class="mono num"><?= (int)($v['views'] ?? 0) ?></td>
        <td class="mono num"><?= (int)($v['favorites'] ?? 0) ?></td>
        <td class="row-actions">
          <a href="<?= url('/viatura/'.$v['slug']) ?>" target="_blank">Ver</a>
          <a href="<?= url('/admin/viaturas/editar/'.$v['id']) ?>">Editar</a>
          <form method="post" action="<?= url('/admin/viaturas/eliminar/'.$v['id']) ?>" onsubmit="return confirm('Eliminar esta viatura?')">
            <?= csrf_field() ?>
            <button type="submit" class="link-danger">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>
