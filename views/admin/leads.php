<?php /* Contactos / leads — $page, $q, $sort, $dir */
$leads = $page['items'];
$ctx = ['q' => $q, 'sort' => $sort, 'dir' => $dir];
?>
<div class="page-head"><h1>Contactos <span class="count-badge"><?= $page['total'] ?></span></h1></div>

<form class="admin-toolbar" method="get" action="<?= url('/admin/leads') ?>">
  <div class="admin-search">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Pesquisar nome, telefone, viatura...">
  </div>
  <button class="btn btn-outline" type="submit">Procurar</button>
  <?php if ($q): ?><a class="btn btn-ghost" href="<?= url('/admin/leads') ?>">Limpar</a><?php endif; ?>
</form>

<div class="panel">
  <div class="table-scroll">
  <table class="data-table">
    <thead>
      <tr>
        <?= sort_th('Data',    'date',    $sort, $dir, $ctx) ?>
        <?= sort_th('Nome',    'name',    $sort, $dir, $ctx) ?>
        <th>Telefone</th>
        <?= sort_th('Viatura', 'vehicle', $sort, $dir, $ctx) ?>
        <th>Mensagem</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$leads): ?>
        <tr><td colspan="6" class="empty-row"><?= $q ? 'Sem resultados.' : 'Ainda não há contactos.' ?></td></tr>
      <?php endif; ?>
      <?php foreach ($leads as $l): ?>
      <tr>
        <td class="mono muted"><?= e(date('d/m/Y H:i', strtotime($l['created_at']))) ?></td>
        <td><strong><?= e($l['name']) ?></strong><?= $l['email'] ? '<div class="muted">'.e($l['email']).'</div>' : '' ?></td>
        <td class="mono"><?= e($l['phone']) ?></td>
        <td><?= $l['vehicle_slug'] ? '<a href="'.url('/viatura/'.$l['vehicle_slug']).'" target="_blank">'.e(($l['brand_name'].' '.$l['model'])).'</a>' : '<span class="muted">— geral / venda</span>' ?></td>
        <td class="lead-msg"><?= nl2br(e($l['message'] ?: '')) ?></td>
        <td class="row-actions">
          <form method="post" action="<?= url('/admin/leads/eliminar/'.$l['id']) ?>" onsubmit="return confirm('Eliminar este contacto?')">
            <?= csrf_field() ?><button class="link-danger" type="submit">Eliminar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>

<?= pagination_html($page['page'], $page['pages'], $page['total'], $ctx) ?>
