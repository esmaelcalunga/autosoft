<?php /* Marcas — $page, $q, $sort, $dir */
$brands = $page['items'];
$ctx = ['q' => $q, 'sort' => $sort, 'dir' => $dir];
?>
<div class="page-head"><h1>Marcas <span class="count-badge"><?= $page['total'] ?></span></h1></div>

<form class="admin-toolbar" method="get" action="<?= url('/admin/marcas') ?>">
  <div class="admin-search">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Pesquisar marca...">
  </div>
  <button class="btn btn-outline" type="submit">Procurar</button>
  <?php if ($q): ?><a class="btn btn-ghost" href="<?= url('/admin/marcas') ?>">Limpar</a><?php endif; ?>
</form>

<div class="two-col">
  <div class="panel">
    <div class="table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <?= sort_th('Marca', 'name', $sort, $dir, $ctx) ?>
          <th>Slug</th>
          <?= sort_th('Viaturas', 'n', $sort, $dir, $ctx, 'num') ?>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$brands): ?>
          <tr><td colspan="4" class="empty-row"><?= $q ? 'Sem resultados.' : 'Sem marcas.' ?></td></tr>
        <?php endif; ?>
        <?php foreach ($brands as $b): ?>
        <tr>
          <td><strong><?= e($b['name']) ?></strong></td>
          <td class="mono muted">/marca/<?= e($b['slug']) ?></td>
          <td class="mono num"><?= (int)$b['n'] ?></td>
          <td class="row-actions">
            <form method="post" action="<?= url('/admin/marcas/eliminar/'.$b['id']) ?>" onsubmit="return confirm('Eliminar a marca e TODAS as suas viaturas?')">
              <?= csrf_field() ?><button class="link-danger" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>
  <div class="panel form-panel side-form">
    <h3>Nova marca</h3>
    <form method="post" action="<?= url('/admin/marcas') ?>">
      <?= csrf_field() ?>
      <label class="field">Nome<input type="text" name="name" placeholder="Ex.: Volkswagen" required></label>
      <button class="btn btn-primary btn-block" type="submit">Adicionar marca</button>
    </form>
  </div>
</div>

<?= pagination_html($page['page'], $page['pages'], $page['total'], $ctx) ?>
