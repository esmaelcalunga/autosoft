<?php /* Formulário de viatura — $vehicle (ou null), $images, $brands, $categories */
$v = $vehicle;
$val = fn($k, $d = '') => e($v[$k] ?? $d);
$isEdit = (bool) $v;
$action = $isEdit ? url('/admin/viaturas/editar/'.$v['id']) : url('/admin/viaturas/nova');
?>
<div class="page-head">
  <h1><?= $isEdit ? 'Editar viatura' : 'Nova viatura' ?></h1>
  <a class="btn btn-outline" href="<?= url('/admin/viaturas') ?>">← Voltar</a>
</div>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data" class="form-grid">
  <?= csrf_field() ?>

  <div class="panel form-panel">
    <h3>Identificação</h3>
    <div class="field-row">
      <label class="field">Marca *
        <select name="brand_id" required>
          <option value="">— escolher —</option>
          <?php foreach ($brands as $b): ?>
            <option value="<?= $b['id'] ?>" <?= ($v['brand_id'] ?? '') == $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label class="field">Categoria
        <select name="category_id">
          <option value="">— nenhuma —</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($v['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div class="field-row">
      <label class="field">Modelo *<input type="text" name="model" value="<?= $val('model') ?>" placeholder="Ex.: Hilux" required></label>
      <label class="field">Versão<input type="text" name="version" value="<?= $val('version') ?>" placeholder="Ex.: 2.8 SRX 4x4 AT"></label>
    </div>
  </div>

  <div class="panel form-panel">
    <h3>Ficha técnica</h3>
    <div class="field-row">
      <label class="field">Ano<input type="text" name="year" value="<?= $val('year') ?>" placeholder="2023/2023"></label>
      <label class="field">Quilometragem<input type="text" name="km" value="<?= $val('km') ?>" placeholder="41200"></label>
    </div>
    <div class="field-row">
      <label class="field">Combustível
        <select name="fuel">
          <?php foreach (['','Gasolina','Gasóleo','Híbrida','Elétrica'] as $f): ?>
            <option value="<?= e($f) ?>" <?= ($v['fuel'] ?? '') === $f ? 'selected' : '' ?>><?= $f === '' ? '— escolher —' : e($f) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label class="field">Caixa
        <select name="transmission">
          <?php foreach (['','Manual','Automática'] as $t): ?>
            <option value="<?= e($t) ?>" <?= ($v['transmission'] ?? '') === $t ? 'selected' : '' ?>><?= $t === '' ? '— escolher —' : e($t) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div class="field-row">
      <label class="field">Potência<input type="text" name="power" value="<?= $val('power') ?>" placeholder="204 cv"></label>
      <label class="field">Cor<input type="text" name="color" value="<?= $val('color') ?>" placeholder="Branco Pérola"></label>
    </div>
  </div>

  <div class="panel form-panel">
    <h3>Preço &amp; localização</h3>
    <div class="field-row">
      <label class="field">Preço (Kz) *<input type="text" name="price" value="<?= $val('price') ?>" placeholder="38500000"></label>
      <label class="field">Prestação<input type="text" name="installment" value="<?= $val('installment') ?>" placeholder="ou 48x de Kz 870.000"></label>
    </div>
    <div class="field-row">
      <label class="field">Localização<input type="text" name="location" value="<?= $val('location') ?>" placeholder="Luanda"></label>
      <label class="field">Etiquetas (separadas por vírgula)<input type="text" name="badges" value="<?= $val('badges') ?>" placeholder="4x4, Blindada"></label>
    </div>
  </div>

  <div class="panel form-panel">
    <h3>Estado &amp; descrição</h3>
    <div class="field-row">
      <label class="field">Condição
        <select name="condition">
          <option value="seminova" <?= ($v['condition'] ?? 'seminova')==='seminova'?'selected':'' ?>>Seminova</option>
          <option value="novo" <?= ($v['condition'] ?? '')==='novo'?'selected':'' ?>>Novo (0 km)</option>
        </select>
      </label>
      <label class="field">Disponibilidade
        <select name="status">
          <?php foreach (['disponivel'=>'Disponível','reservado'=>'Reservado','vendido'=>'Vendido'] as $k=>$lbl): ?>
            <option value="<?= $k ?>" <?= ($v['status'] ?? 'disponivel')===$k?'selected':'' ?>><?= e($lbl) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <label class="field check-field">
      <input type="checkbox" name="featured" value="1" <?= !empty($v['featured']) ? 'checked' : '' ?>> Destacar na página inicial
    </label>
    <label class="field">Descrição
      <textarea name="description" rows="4" placeholder="Descrição da viatura"><?= $val('description') ?></textarea>
    </label>
  </div>

  <div class="panel form-panel">
    <h3>Fotografias <?php if (!empty($images)): ?><span class="count-badge"><?= count($images) ?></span><?php endif; ?></h3>
    <?php if (!empty($images)): ?>
      <div class="img-grid">
        <?php foreach ($images as $i => $img): $isMain = $i === 0; ?>
          <div class="img-thumb<?= $isMain ? ' is-main' : '' ?>">
            <img src="<?= upload_url($img['path']) ?>" alt="">
            <?php if ($isMain): ?><span class="img-main-badge">Principal</span><?php endif; ?>
            <div class="img-actions">
              <?php if (!$isMain): ?>
                <form method="post" action="<?= url('/admin/viaturas/imagem/principal/'.$img['id']) ?>" class="inline-form">
                  <?= csrf_field() ?>
                  <button type="submit" class="img-act" title="Tornar principal">★</button>
                </form>
              <?php endif; ?>
              <form method="post" action="<?= url('/admin/viaturas/imagem/eliminar/'.$img['id']) ?>" class="inline-form" onsubmit="return confirm('Remover esta imagem?')">
                <?= csrf_field() ?>
                <button type="submit" class="img-act img-act-del" title="Remover">×</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <p class="hint">A primeira imagem é a <strong>principal</strong> (capa). Use ★ para tornar outra principal.</p>
    <?php endif; ?>
    <div class="upload-zone" id="upload-zone">
      <label class="field upload-label">
        <span class="upload-label-title">Adicionar fotos ou vídeos</span>
        <span class="upload-label-sub">JPG, PNG, WEBP, MP4, WEBM, MOV — pode escolher vários</span>
        <input type="file" name="images[]" accept="image/*,video/*" multiple id="upload-input">
      </label>
      <div class="upload-previews" id="upload-previews" hidden></div>
      <div class="upload-progress" id="upload-progress" hidden>
        <div class="upload-progress-track"><div class="upload-progress-bar" id="upload-progress-bar"></div></div>
        <div class="upload-progress-meta">
          <span id="upload-progress-text">0%</span>
          <span id="upload-progress-size"></span>
        </div>
      </div>
    </div>
    <?php if (empty($images)): ?>
      <p class="hint">A <strong>primeira</strong> media que enviar será a capa da viatura. As restantes ficam como secundárias na galeria pública.</p>
    <?php endif; ?>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary btn-lg"><?= $isEdit ? 'Guardar alterações' : 'Criar viatura' ?></button>
    <a class="btn btn-ghost" href="<?= url('/admin/viaturas') ?>">Cancelar</a>
  </div>
</form>
