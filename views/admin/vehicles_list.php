<?php /* Lista de viaturas — $vehicles */ ?>
<div class="page-head">
  <h1>Viaturas <span class="count-badge"><?= count($vehicles) ?></span></h1>
  <a class="btn btn-primary" href="<?= url('/admin/viaturas/nova') ?>">+ Nova viatura</a>
</div>

<div class="panel">
  <table class="data-table">
    <thead>
      <tr><th>Viatura</th><th>Marca</th><th>Categoria</th><th>Ano</th><th>Preço</th><th>Estado</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (!$vehicles): ?>
        <tr><td colspan="7" class="empty-row">Ainda não há viaturas. <a href="<?= url('/admin/viaturas/nova') ?>">Adicione a primeira</a>.</td></tr>
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
