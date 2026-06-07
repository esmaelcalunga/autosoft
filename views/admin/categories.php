<?php /* Categorias — $page, $q, $sort, $dir */
$categories = $page['items'];
$ctx = ['q' => $q, 'sort' => $sort, 'dir' => $dir];
?>
<div class="page-head"><h1>Categorias <span class="count-badge"><?= $page['total'] ?></span></h1></div>

<form class="admin-toolbar" method="get" action="<?= url('/admin/categorias') ?>">
  <div class="admin-search">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Pesquisar categoria...">
  </div>
  <button class="btn btn-outline" type="submit">Procurar</button>
  <?php if ($q): ?><a class="btn btn-ghost" href="<?= url('/admin/categorias') ?>">Limpar</a><?php endif; ?>
</form>

<div class="two-col">
  <div class="panel">
    <div class="table-scroll">
    <table class="data-table">
      <thead>
        <tr>
          <?= sort_th('Categoria', 'name', $sort, $dir, $ctx) ?>
          <th>Slug</th>
          <?= sort_th('Viaturas', 'n', $sort, $dir, $ctx, 'num') ?>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$categories): ?>
          <tr><td colspan="4" class="empty-row"><?= $q ? 'Sem resultados.' : 'Sem categorias.' ?></td></tr>
        <?php endif; ?>
        <?php foreach ($categories as $c): ?>
        <tr>
          <td><strong><?= e($c['name']) ?></strong><div class="muted"><?= e($c['description'] ?: '') ?></div></td>
          <td class="mono muted">/categoria/<?= e($c['slug']) ?></td>
          <td class="mono num"><?= (int)$c['n'] ?></td>
          <td class="row-actions">
            <form method="post" action="<?= url('/admin/categorias/eliminar/'.$c['id']) ?>" onsubmit="return confirm('Eliminar esta categoria?')">
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
    <h3>Nova categoria</h3>
    <form method="post" action="<?= url('/admin/categorias') ?>">
      <?= csrf_field() ?>
      <label class="field">Nome<input type="text" name="name" placeholder="Ex.: Coupé" required></label>
      <label class="field">Descrição<input type="text" name="description" placeholder="Opcional"></label>
      <button class="btn btn-primary btn-block" type="submit">Adicionar categoria</button>
    </form>
  </div>
</div>

<?= pagination_html($page['page'], $page['pages'], $page['total'], $ctx) ?>
