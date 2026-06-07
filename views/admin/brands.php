<?php /* Marcas — $brands */ ?>
<div class="page-head"><h1>Marcas</h1></div>
<div class="two-col">
  <div class="panel">
    <table class="data-table">
      <thead><tr><th>Marca</th><th>Slug</th><th>Viaturas</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($brands as $b): ?>
        <tr>
          <td><strong><?= e($b['name']) ?></strong></td>
          <td class="mono muted">/marca/<?= e($b['slug']) ?></td>
          <td class="mono"><?= (int)$b['n'] ?></td>
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
  <div class="panel form-panel side-form">
    <h3>Nova marca</h3>
    <form method="post" action="<?= url('/admin/marcas') ?>">
      <?= csrf_field() ?>
      <label class="field">Nome<input type="text" name="name" placeholder="Ex.: Volkswagen" required></label>
      <button class="btn btn-primary btn-block" type="submit">Adicionar marca</button>
    </form>
  </div>
</div>
