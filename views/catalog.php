<?php /* Catálogo — $vehicles, $brands, $categories, $fuels, $filters, $heading, $eyebrow, $intro */
$navActive = 'catalog';
$lockBrand = $lockBrand ?? null;
$lockCategory = $lockCategory ?? null;
?>
<div class="container section">
  <div class="catalog-head">
    <span class="eyebrow"><?= e($eyebrow) ?></span>
    <h1><?= e($heading) ?></h1>
    <?php if (!empty($intro)): ?><p class="catalog-intro"><?= e($intro) ?></p><?php endif; ?>
  </div>

  <div class="catalog-layout">
    <!-- FILTROS -->
    <aside class="filter-rail">
      <form method="get" action="<?= $lockBrand ? url('/marca/' . $lockBrand['slug']) : ($lockCategory ? url('/categoria/' . $lockCategory['slug']) : url('/estoque')) ?>">
        <div class="filter-title">Filtros</div>

        <?php if (!$lockBrand): ?>
        <div class="filter-group">
          <h4>Marca</h4>
          <select name="marca">
            <option value="">Todas as marcas</option>
            <?php foreach ($brands as $b): ?>
              <option value="<?= e($b['slug']) ?>" <?= $filters['brand_slug'] === $b['slug'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>

        <?php if (!$lockCategory): ?>
        <div class="filter-group">
          <h4>Categoria</h4>
          <select name="categoria">
            <option value="">Todas</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= e($c['slug']) ?>" <?= $filters['category_slug'] === $c['slug'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>

        <div class="filter-group">
          <h4>Combustível</h4>
          <select name="combustivel">
            <option value="">Todos</option>
            <?php foreach ($fuels as $f): ?>
              <option value="<?= e($f) ?>" <?= $filters['fuel'] === $f ? 'selected' : '' ?>><?= e($f) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="filter-group">
          <h4>Preço máximo</h4>
          <select name="preco_max">
            <option value="">Qualquer</option>
            <?php foreach ([25000000, 40000000, 70000000, 100000000] as $p): ?>
              <option value="<?= $p ?>" <?= (string)$filters['max_price'] === (string)$p ? 'selected' : '' ?>><?= kz($p) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <button class="btn btn-primary btn-block" type="submit">Aplicar filtros</button>
        <a class="btn btn-ghost btn-block" href="<?= $lockBrand ? url('/marca/' . $lockBrand['slug']) : ($lockCategory ? url('/categoria/' . $lockCategory['slug']) : url('/estoque')) ?>">Limpar</a>
      </form>
    </aside>

    <!-- RESULTADOS -->
    <div class="catalog-results">
      <div class="results-bar">
        <span class="results-count"><?= count($vehicles) ?> resultado<?= count($vehicles) !== 1 ? 's' : '' ?></span>
        <form method="get" class="sort-form">
          <?php foreach (['marca'=>$filters['brand_slug'],'categoria'=>$filters['category_slug'],'combustivel'=>$filters['fuel'],'preco_max'=>$filters['max_price'],'q'=>$filters['search']] as $k=>$val): if ($val !== '' && $val !== null): ?>
            <input type="hidden" name="<?= e($k) ?>" value="<?= e($val) ?>">
          <?php endif; endforeach; ?>
          <label>Ordenar
            <select name="ordenar" onchange="this.form.submit()">
              <option value="relevance" <?= $filters['sort']==='relevance'?'selected':'' ?>>Mais relevantes</option>
              <option value="price-asc" <?= $filters['sort']==='price-asc'?'selected':'' ?>>Menor preço</option>
              <option value="price-desc" <?= $filters['sort']==='price-desc'?'selected':'' ?>>Maior preço</option>
              <option value="km-asc" <?= $filters['sort']==='km-asc'?'selected':'' ?>>Menor km</option>
            </select>
          </label>
        </form>
      </div>

      <?php if ($vehicles): ?>
        <div class="grid-3">
          <?php foreach ($vehicles as $v) { echo vehicle_card($v); } ?>
        </div>
      <?php else: ?>
        <div class="empty">Nenhuma viatura encontrada com esses filtros.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
