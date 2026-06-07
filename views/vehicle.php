<?php /* Detalhe — $v, $images, $related */
$navActive = 'catalog';
$badges = [];
if ((int)$v['km'] === 0) { $badges[] = ['0 km','accent']; }
elseif ($v['condition'] === 'seminova') { $badges[] = ['Seminova','ink']; }
foreach (parse_badges($v['badges'] ?? '') as $b) {
  $badges[] = [$b, in_array($b, ['Premium','Blindada']) ? 'accent' : 'neutral'];
}
$specs = [
  ['Ano', substr((string)$v['year'],0,4) ?: $v['year']],
  ['Km', number_format((int)$v['km'],0,',','.')],
  ['Combustível', $v['fuel']],
  ['Caixa', $v['transmission']],
  ['Potência', $v['power']],
  ['Cor', $v['color']],
];
?>
<div class="container section detail">
  <a class="back-link" href="<?= url('/estoque') ?>">← Voltar ao stock</a>

  <div class="detail-grid">
    <!-- ESQUERDA -->
    <div class="detail-main">
      <div class="detail-badges">
        <?php foreach ($badges as [$txt,$tone]): ?><span class="badge badge-<?= $tone ?>"><?= e($txt) ?></span><?php endforeach; ?>
      </div>

      <?php if ($images): ?>
        <div class="gallery-main" id="gallery-main">
          <img src="<?= upload_url($images[0]['path']) ?>" alt="<?= e($v['brand_name'].' '.$v['model']) ?>" id="gallery-main-img">
          <?php if (count($images) > 1): ?>
            <span class="gallery-counter"><span id="gallery-counter-current">1</span> / <?= count($images) ?></span>
          <?php endif; ?>
        </div>
        <?php if (count($images) > 1): ?>
        <div class="gallery-thumbs">
          <?php foreach ($images as $i => $img): ?>
            <button type="button" class="thumb<?= $i === 0 ? ' is-active' : '' ?>" data-gallery-src="<?= e(upload_url($img['path'])) ?>" data-gallery-index="<?= $i + 1 ?>" aria-label="Ver foto <?= $i + 1 ?>">
              <img src="<?= upload_url($img['path']) ?>" alt="">
            </button>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="gallery-main ph-stripes">
          <div class="ph-inner">
            <svg viewBox="0 0 48 48" width="34" height="34"><polygon points="4,39 11,39 19,9 12,9" fill="var(--ink-300)"/><polygon points="16,39 23,39 31,9 24,9" fill="var(--ink-300)"/><polygon points="28,39 35,39 43,9 36,9" fill="var(--red-300)"/></svg>
            <span>Galeria de fotos da viatura</span>
          </div>
        </div>
        <div class="gallery-thumbs">
          <?php for ($i=0;$i<4;$i++): ?><div class="thumb ph-stripes"></div><?php endfor; ?>
        </div>
      <?php endif; ?>

      <div class="detail-section">
        <h3>Ficha técnica</h3>
        <div class="spec-grid">
          <?php foreach ($specs as [$k,$val]): ?>
            <div class="spec-cell"><span class="spec-k"><?= e($k) ?></span><span class="spec-v"><?= e($val) ?></span></div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="detail-section">
        <h3>Sobre esta viatura</h3>
        <p class="detail-desc"><?= e($v['description'] ?: ($v['brand_name'].' '.$v['model'].' '.$v['version'].' em excelente estado, com inspeção técnica aprovada e garantia AutoSOFT.')) ?></p>
        <div class="tag-row">
          <span class="tag">Inspeção técnica aprovada</span>
          <span class="tag">Revisões em dia</span>
          <span class="tag">Aceita retoma</span>
          <span class="tag">Documentação em dia</span>
        </div>
      </div>
    </div>

    <!-- DIREITA — caixa de preço -->
    <aside class="detail-aside">
      <div class="price-box">
        <span class="eyebrow"><?= e($v['location']) ?></span>
        <h1 class="price-title"><?= e($v['brand_name'].' '.$v['model']) ?></h1>
        <p class="price-version"><?= e($v['version']) ?></p>
        <div class="tag-row">
          <span class="tag"><?= e(substr((string)$v['year'],0,4)) ?></span>
          <span class="tag"><?= number_format((int)$v['km'],0,',','.') ?> km</span>
          <span class="tag"><?= e($v['transmission']) ?></span>
        </div>
        <div class="price-block">
          <div class="price-label">Preço</div>
          <div class="price-value"><?= e(kz($v['price'])) ?></div>
          <?php if ($v['installment']): ?><div class="price-install"><?= e($v['installment']) ?></div><?php endif; ?>
        </div>
        <a class="btn btn-lg btn-primary btn-block" href="#interesse">Tenho interesse →</a>
        <a class="btn btn-outline btn-block" href="tel:+244900000000">Ligar</a>
        <button type="button" class="btn btn-ghost btn-block fav-btn-lg" data-fav-slug="<?= e($v['slug']) ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          <span class="fav-btn-lg-label">Guardar nos favoritos</span>
        </button>
      </div>

      <form class="lead-box" id="interesse" method="post" action="<?= url('/viatura/'.$v['slug']) ?>">
        <?= csrf_field() ?>
        <h4>Falar com um consultor</h4>
        <input type="text" name="name" placeholder="Nome" required>
        <input type="text" name="phone" placeholder="Telefone / WhatsApp" required>
        <input type="email" name="email" placeholder="Email (opcional)">
        <textarea name="message" rows="3" placeholder="Mensagem (opcional)"></textarea>
        <button class="btn btn-primary btn-block" type="submit">Enviar pedido</button>
        <span class="mono-note">Resposta em ~5 minutos em horário comercial.</span>
      </form>
    </aside>
  </div>

  <?php if ($related): ?>
  <section class="related">
    <div class="section-head">
      <div><span class="eyebrow">Também lhe pode interessar</span><h2>Viaturas relacionadas</h2></div>
      <a class="btn btn-ghost" href="<?= url('/estoque') ?>">Ver todo o stock →</a>
    </div>
    <div class="grid-4">
      <?php foreach ($related as $r) { echo vehicle_card($r); } ?>
    </div>
  </section>
  <?php endif; ?>
</div>
