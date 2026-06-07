<?php
/* =====================================================================
 *  AutoSOFT — Controlador do site público
 * ===================================================================== */

function page_home(): void
{
    $featured = find_vehicles([
        'only_available' => true,
        'featured'       => true,
        'limit'          => 6,
    ]);
    if (count($featured) < 6) {
        $featured = find_vehicles(['only_available' => true, 'limit' => 6]);
    }

    render('home', [
        'title'      => 'AutoSOFT — Seu próximo carro',
        'featured'   => $featured,
        'brands'     => all_brands(),
        'categories' => all_categories(),
    ]);
}

/** Catálogo com filtros via query string. */
function page_catalog(array $preset = [], array $context = []): void
{
    $filters = array_merge([
        'only_available' => true,
        'brand_slug'     => q('marca'),
        'category_slug'  => q('categoria'),
        'fuel'           => q('combustivel'),
        'max_price'      => q('preco_max'),
        'search'         => q('q'),
        'sort'           => q('ordenar', 'relevance'),
    ], $preset);

    $vehicles = find_vehicles($filters);

    render('catalog', array_merge([
        'title'      => $context['title'] ?? 'Stock — AutoSOFT',
        'heading'    => $context['heading'] ?? 'Todas as viaturas',
        'eyebrow'    => $context['eyebrow'] ?? 'Stock',
        'intro'      => $context['intro'] ?? null,
        'vehicles'   => $vehicles,
        'brands'     => all_brands(),
        'categories' => all_categories(),
        'fuels'      => distinct_fuels(),
        'filters'    => $filters,
    ], $context));
}

function page_brand(string $slug): void
{
    $brand = brand_by_slug($slug);
    if (!$brand) { page_not_found(); return; }

    page_catalog(
        ['brand_slug' => $slug],
        [
            'title'   => $brand['name'] . ' — Stock AutoSOFT',
            'eyebrow' => 'Marca',
            'heading' => $brand['name'],
            'intro'   => 'Todas as viaturas ' . $brand['name'] . ' disponíveis no nosso stock.',
            'lockBrand' => $brand,
        ]
    );
}

function page_category(string $slug): void
{
    $cat = category_by_slug($slug);
    if (!$cat) { page_not_found(); return; }

    page_catalog(
        ['category_slug' => $slug],
        [
            'title'   => $cat['name'] . ' — Stock AutoSOFT',
            'eyebrow' => 'Categoria',
            'heading' => $cat['name'],
            'intro'   => $cat['description'] ?: ('Viaturas da categoria ' . $cat['name'] . '.'),
            'lockCategory' => $cat,
        ]
    );
}

function page_vehicle(string $slug): void
{
    $v = vehicle_by_slug($slug);
    if (!$v) { page_not_found(); return; }

    $images = vehicle_images((int) $v['id']);

    // Relacionadas: mesma marca primeiro, depois preço próximo
    $related = find_vehicles([
        'only_available' => true,
        'brand_slug'     => $v['brand_slug'],
        'limit'          => 8,
    ]);
    $related = array_values(array_filter($related, fn($r) => $r['id'] != $v['id']));
    if (count($related) < 4) {
        $more = find_vehicles(['only_available' => true, 'limit' => 8]);
        foreach ($more as $m) {
            if ($m['id'] != $v['id'] && !in_array($m['id'], array_column($related, 'id'))) {
                $related[] = $m;
            }
        }
    }
    $related = array_slice($related, 0, 4);

    render('vehicle', [
        'title'   => $v['brand_name'] . ' ' . $v['model'] . ' — AutoSOFT',
        'v'       => $v,
        'images'  => $images,
        'related' => $related,
    ]);
}

/** Processa o formulário "Tenho interesse". */
function page_vehicle_lead(string $slug): void
{
    csrf_check();
    $v = vehicle_by_slug($slug);
    if (!$v) { page_not_found(); return; }

    $name  = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg   = trim($_POST['message'] ?? '');

    if ($name === '' || $phone === '') {
        flash('Indique pelo menos o nome e o telefone.', 'error');
        redirect('/viatura/' . $slug);
    }

    $st = db()->prepare('INSERT INTO leads (vehicle_id, name, phone, email, message) VALUES (?, ?, ?, ?, ?)');
    $st->execute([$v['id'], $name, $phone, $email ?: null, $msg ?: null]);

    flash('Pedido enviado! Um consultor entrará em contacto consigo em breve.', 'success');
    redirect('/viatura/' . $slug);
}

function page_sell(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_check();
        $name  = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        if ($name !== '' && $phone !== '') {
            $details = sprintf(
                "[Venda] %s %s · Ano %s · %s km\n%s",
                $_POST['brand'] ?? '', $_POST['model'] ?? '',
                $_POST['year'] ?? '-', $_POST['km'] ?? '-',
                $_POST['message'] ?? ''
            );
            $st = db()->prepare('INSERT INTO leads (vehicle_id, name, phone, email, message) VALUES (NULL, ?, ?, NULL, ?)');
            $st->execute([$name, $phone, $details]);
            redirect('/vender?ok=1');
        }
    }
    render('sell', ['title' => 'Vender a minha viatura — AutoSOFT']);
}

function page_about(): void
{
    render('about', ['title' => 'A AutoSOFT']);
}

function page_not_found(): void
{
    http_response_code(404);
    render('404', ['title' => 'Página não encontrada — AutoSOFT']);
}
