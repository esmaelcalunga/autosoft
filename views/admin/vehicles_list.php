<?php /* Lista de viaturas — $page, $q, $sort, $dir */
$vehicles = $page['items'];
$ctx = ['q' => $q, 'sort' => $sort, 'dir' => $dir];
?>
<div class="page-head">
  <h1>Viaturas <span class="count-badge"><?= $page['total'] ?></span></h1>
  <a class="btn btn-primary" href="<?= url('/admin/viaturas/nova') ?>">+ Nova viatura</a>
</div>

<form class="admin-toolbar" method="get" action="<?= url('/admin/viaturas') ?>">
  <div class="admin-search">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Pesquisar modelo, versão ou marca...">
  </div>
  <button class="btn btn-outline" type="submit">Procurar</button>
  <?php if ($q): ?><a class="btn btn-ghost" href="<?= url('/admin/viaturas') ?>">Limpar</a><?php endif; ?>
</form>

<div class="panel">
  <div class="table-scroll">
  <table class="data-table">
    <thead>
      <tr>
        <?= sort_th('Viatura',    'model',     $sort, $dir, $ctx) ?>
        <?= sort_th('Marca',      'brand',     $sort, $dir, $ctx) ?>
        <?= sort_th('Categoria',  'category',  $sort, $dir, $ctx) ?>
        <?= sort_th('Ano',        'year',      $sort, $dir, $ctx) ?>
        <?= sort_th('Preço',      'price',     $sort, $dir, $ctx, 'num') ?>
        <?= sort_th('Estado',     'status',    $sort, $dir, $ctx) ?>
        <?= sort_th('👁',         'views',     $sort, $dir, $ctx, 'num') ?>
        <?= sort_th('❤',          'favorites', $sort, $dir, $ctx, 'num') ?>
        <?= sort_th('📞',         'contacts',  $sort, $dir, $ctx, 'num') ?>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$vehicles): ?>
        <tr><td colspan="10" class="empty-row">
          <?= $q ? 'Sem resultados para "'.e($q).'".' : 'Ainda não há viaturas.' ?>
          <?php if (!$q): ?> <a href="<?= url('/admin/viaturas/nova') ?>">Adicione a primeira</a>.<?php endif; ?>
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
        <td class="mono num"><?= e(kz($v['price'])) ?></td>
        <td><span class="pill pill-<?= e($v['status']) ?>"><?= e($v['status']) ?></span></td>
        <td class="mono num"><?= (int)($v['views'] ?? 0) ?></td>
        <td class="mono num"><?= (int)($v['favorites'] ?? 0) ?></td>
        <td class="mono num"><?= (int)($v['contacts'] ?? 0) ?></td>
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

<?= pagination_html($page['page'], $page['pages'], $page['total'], $ctx) ?>
