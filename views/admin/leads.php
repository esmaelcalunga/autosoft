<?php /* Contactos / leads — $leads */ ?>
<div class="page-head"><h1>Contactos <span class="count-badge"><?= count($leads) ?></span></h1></div>
<div class="panel">
  <table class="data-table">
    <thead><tr><th>Data</th><th>Nome</th><th>Telefone</th><th>Viatura</th><th>Mensagem</th><th></th></tr></thead>
    <tbody>
      <?php if (!$leads): ?>
        <tr><td colspan="6" class="empty-row">Ainda não há contactos.</td></tr>
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
