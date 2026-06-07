<?php
/* =====================================================================
 *  AutoSOFT — Componentes de apresentação (HTML reutilizável)
 * ===================================================================== */

/** Cartão de viatura para grelhas. $v = linha de find_vehicles(). */
function vehicle_card(array $v): string
{
    $href = url('/viatura/' . $v['slug']);
    $name = e($v['brand_name'] . ' ' . $v['model']);
    $year = e(substr((string) $v['year'], 0, 4));
    $km   = number_format((int) $v['km'], 0, ',', '.');

    // Badges: condição + extras + 0 km
    $badges = '';
    if ((int) $v['km'] === 0) {
        $badges .= '<span class="badge badge-accent">0 km</span>';
    } elseif ($v['condition'] === 'seminova') {
        $badges .= '<span class="badge badge-ink">Seminova</span>';
    }
    foreach (parse_badges($v['badges'] ?? '') as $b) {
        $tone = in_array($b, ['Premium', 'Blindada']) ? 'badge-accent' : 'badge-neutral';
        $badges .= '<span class="badge ' . $tone . '">' . e($b) . '</span>';
    }

    // Media: imagem, vídeo ou placeholder listrado
    if (!empty($v['cover'])) {
        $coverUrl = upload_url($v['cover']);
        if (media_type($v['cover']) === 'video') {
            $media = '<video src="' . $coverUrl . '" muted playsinline preload="metadata"></video>'
                   . '<span class="vcard-video-tag">▶ Vídeo</span>';
        } else {
            $media = '<img src="' . $coverUrl . '" alt="' . $name . '" loading="lazy">';
        }
    } else {
        $media = '<div class="ph-stripes"><svg viewBox="0 0 48 48" width="34" height="34">'
            . '<polygon points="4,39 11,39 19,9 12,9" fill="var(--ink-300)"/>'
            . '<polygon points="16,39 23,39 31,9 24,9" fill="var(--ink-300)"/>'
            . '<polygon points="28,39 35,39 43,9 36,9" fill="var(--red-300)"/></svg>'
            . '<span>Foto da viatura</span></div>';
    }

    $installment = $v['installment'] ? '<div class="card-install">' . e($v['installment']) . '</div>' : '';
    $kzPrice = kz($v['price']);
    $version = e($v['version']);
    $fuel    = e($v['fuel']);
    $loc     = e($v['location']);

    $slug = e($v['slug']);

    return <<<HTML
<div class="vcard-wrap">
  <button type="button" class="fav-btn" data-fav-slug="{$slug}" aria-label="Guardar nos favoritos">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
  </button>
  <a class="vcard" href="{$href}">
    <div class="vcard-media">
      {$media}
      <div class="vcard-badges">{$badges}</div>
    </div>
    <div class="vcard-body">
      <h3 class="vcard-title">{$name}</h3>
      <p class="vcard-version">{$version}</p>
      <div class="vcard-specs">
        <span>{$year}</span><i></i><span>{$km} km</span><i></i><span>{$fuel}</span>
      </div>
      <div class="vcard-foot">
        <div class="card-price">{$kzPrice}</div>
        {$installment}
      </div>
      <div class="vcard-loc">{$loc}</div>
    </div>
  </a>
</div>
HTML;
}
