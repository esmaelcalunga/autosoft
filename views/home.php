<?php /* Home — $featured, $brands, $categories */ $navActive = 'home'; ?>
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <span class="eyebrow">Stand premium · Seminovas &amp; 0 km</span>
      <h1>Velocidade em cada<br>detalhe. <span class="accent">Segurança</span><br>em cada negócio.</h1>
      <p>Mais de 600 viaturas com proveniência verificada, inspeção técnica e garantia.
         Escolha a sua e conduza ainda esta semana.</p>
      <div class="hero-cta">
        <a class="btn btn-lg btn-primary" href="<?= url('/estoque') ?>">Ver stock →</a>
        <a class="btn btn-lg btn-outline" href="<?= url('/vender') ?>">Vender a minha viatura</a>
      </div>
    </div>
    <div class="hero-media ph-stripes">
      <div class="ph-inner">
        <svg viewBox="0 0 48 48" width="48" height="48"><polygon points="4,39 11,39 19,9 12,9" fill="var(--ink-300)"/><polygon points="16,39 23,39 31,9 24,9" fill="var(--ink-300)"/><polygon points="28,39 35,39 43,9 36,9" fill="var(--red-400)"/></svg>
        <span>Imagem principal · viatura em destaque</span>
      </div>
      <span class="hero-tag">+600 viaturas em stock</span>
    </div>
  </div>

  <div class="container">
    <form class="quick-search" action="<?= url('/estoque') ?>" method="get">
      <label>Marca
        <select name="marca">
          <option value="">Todas as marcas</option>
          <?php foreach ($brands as $b): ?><option value="<?= e($b['slug']) ?>"><?= e($b['name']) ?></option><?php endforeach; ?>
        </select>
      </label>
      <label>Categoria
        <select name="categoria">
          <option value="">Todas</option>
          <?php foreach ($categories as $c): ?><option value="<?= e($c['slug']) ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
        </select>
      </label>
      <label>Até
        <select name="preco_max">
          <option value="">Qualquer preço</option>
          <option value="25000000">Kz 25.000.000</option>
          <option value="40000000">Kz 40.000.000</option>
          <option value="70000000">Kz 70.000.000</option>
          <option value="100000000">Kz 100.000.000</option>
        </select>
      </label>
      <button class="btn btn-lg btn-primary" type="submit">Pesquisar</button>
    </form>
  </div>
</section>

<section class="container section">
  <div class="section-head">
    <div>
      <span class="eyebrow">Em destaque</span>
      <h2>Selecionadas da semana</h2>
    </div>
    <a class="btn btn-ghost" href="<?= url('/estoque') ?>">Ver todos →</a>
  </div>
  <div class="grid-3">
    <?php foreach ($featured as $v) { echo vehicle_card($v); } ?>
  </div>
</section>

<section class="trust-strip">
  <div class="container trust-grid">
    <?php
    $trust = [
      ['Inspeção técnica', '240 pontos verificados em cada viatura.'],
      ['Garantia real', '12 meses de garantia em todo o stock.'],
      ['Aceitamos retoma', 'Entregue a sua e saia com outra viatura.'],
      ['Nota 4,9', 'Mais de 8.000 clientes satisfeitos.'],
    ];
    foreach ($trust as [$h, $p]): ?>
      <div class="trust-item">
        <span class="trust-ico">★</span>
        <div><h4><?= e($h) ?></h4><p><?= e($p) ?></p></div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="container section">
  <div class="consign">
    <div class="consign-glow"></div>
    <div class="consign-copy">
      <span class="eyebrow eyebrow-red">Venda por consignação</span>
      <h2>Quer vender a sua viatura? Nós tratamos de tudo.</h2>
      <p>Avaliação em 24h, exposição nos nossos stands e toda a documentação por nossa conta.
         Você recebe o melhor valor, em segurança.</p>
    </div>
    <div class="consign-cta">
      <a class="btn btn-lg btn-primary" href="<?= url('/vender') ?>">Vender a minha viatura →</a>
      <span class="mono-note">Avaliação grátis · comissão a partir de 5%</span>
    </div>
  </div>
</section>
