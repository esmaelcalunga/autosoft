<?php /* Favoritos — $vehicles, $hasSlugs */
$navActive = '';
?>
<div class="container section" id="fav-page" data-has-slugs="<?= $hasSlugs ? '1' : '0' ?>">
  <div class="catalog-head">
    <span class="eyebrow">Favoritos</span>
    <h1>As minhas viaturas guardadas</h1>
    <p class="catalog-intro" id="fav-intro">Guardadas apenas neste navegador, sem necessidade de conta.</p>
  </div>

  <div id="fav-empty" class="empty" <?= $vehicles ? 'hidden' : '' ?>>
    Ainda não guardou nenhuma viatura.
    Explore o <a href="<?= url('/estoque') ?>">stock</a> e toque no coração para guardar.
  </div>

  <?php if ($vehicles): ?>
    <div class="grid-3" id="fav-grid">
      <?php foreach ($vehicles as $v) { echo vehicle_card($v); } ?>
    </div>
  <?php endif; ?>
</div>
