<?php /* Categorias — $categories */ ?>
<div class="page-head"><h1>Categorias</h1></div>
<div class="two-col">
  <div class="panel">
    <table class="data-table">
      <thead><tr><th>Categoria</th><th>Slug</th><th>Viaturas</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($categories as $c): ?>
        <tr>
          <td><strong><?= e($c['name']) ?></strong><div class="muted"><?= e($c['description'] ?: '') ?></div></td>
          <td class="mono muted">/categoria/<?= e($c['slug']) ?></td>
          <td class="mono"><?= (int)$c['n'] ?></td>
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
