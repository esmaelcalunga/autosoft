<?php
/* =====================================================================
 *  AutoSOFT — Acesso a dados (queries reutilizáveis)
 * ===================================================================== */

/** Todas as marcas, ordenadas. */
function all_brands(): array
{
    return db()->query('SELECT * FROM brands ORDER BY name')->fetchAll();
}

/** Todas as categorias, ordenadas. */
function all_categories(): array
{
    return db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
}

function brand_by_slug(string $slug): ?array
{
    $st = db()->prepare('SELECT * FROM brands WHERE slug = ? LIMIT 1');
    $st->execute([$slug]);
    return $st->fetch() ?: null;
}

function category_by_slug(string $slug): ?array
{
    $st = db()->prepare('SELECT * FROM categories WHERE slug = ? LIMIT 1');
    $st->execute([$slug]);
    return $st->fetch() ?: null;
}

/** SELECT com JOIN de marca/categoria + imagem de capa. */
function vehicle_select_base(): string
{
    return "SELECT v.*, b.name AS brand_name, b.slug AS brand_slug,
                   c.name AS category_name, c.slug AS category_slug,
                   (SELECT path FROM vehicle_images vi WHERE vi.vehicle_id = v.id
                    ORDER BY vi.sort, vi.id LIMIT 1) AS cover
            FROM vehicles v
            JOIN brands b ON b.id = v.brand_id
            LEFT JOIN categories c ON c.id = v.category_id";
}

/**
 * Lista de viaturas com filtros opcionais.
 * $filters: brand_slug, category_slug, fuel, max_price, sort, only_available, featured, search
 */
function find_vehicles(array $filters = []): array
{
    $sql = vehicle_select_base();
    $where = [];
    $params = [];

    if (!empty($filters['only_available'])) {
        $where[] = "v.status = 'disponivel'";
    }
    if (!empty($filters['featured'])) {
        $where[] = 'v.featured = 1';
    }
    if (!empty($filters['brand_slug'])) {
        $where[] = 'b.slug = ?';
        $params[] = $filters['brand_slug'];
    }
    if (!empty($filters['category_slug'])) {
        $where[] = 'c.slug = ?';
        $params[] = $filters['category_slug'];
    }
    if (!empty($filters['fuel'])) {
        $where[] = 'v.fuel = ?';
        $params[] = $filters['fuel'];
    }
    if (!empty($filters['max_price'])) {
        $where[] = 'v.price <= ?';
        $params[] = (int) $filters['max_price'];
    }
    if (!empty($filters['search'])) {
        $where[] = '(v.model LIKE ? OR v.version LIKE ? OR b.name LIKE ?)';
        $like = '%' . $filters['search'] . '%';
        array_push($params, $like, $like, $like);
    }
    if (!empty($filters['slugs']) && is_array($filters['slugs'])) {
        $ph = implode(',', array_fill(0, count($filters['slugs']), '?'));
        $where[] = "v.slug IN ($ph)";
        foreach ($filters['slugs'] as $s) { $params[] = $s; }
    }

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    switch ($filters['sort'] ?? '') {
        case 'price-asc':  $sql .= ' ORDER BY v.price ASC'; break;
        case 'price-desc': $sql .= ' ORDER BY v.price DESC'; break;
        case 'km-asc':     $sql .= ' ORDER BY v.km ASC'; break;
        default:           $sql .= ' ORDER BY v.featured DESC, v.created_at DESC';
    }

    if (!empty($filters['limit'])) {
        $sql .= ' LIMIT ' . (int) $filters['limit'];
    }

    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function vehicle_by_slug(string $slug): ?array
{
    $st = db()->prepare(vehicle_select_base() . ' WHERE v.slug = ? LIMIT 1');
    $st->execute([$slug]);
    return $st->fetch() ?: null;
}

function vehicle_by_id(int $id): ?array
{
    $st = db()->prepare(vehicle_select_base() . ' WHERE v.id = ? LIMIT 1');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function vehicle_images(int $vehicleId): array
{
    $st = db()->prepare('SELECT * FROM vehicle_images WHERE vehicle_id = ? ORDER BY sort, id');
    $st->execute([$vehicleId]);
    return $st->fetchAll();
}

/** Distintos combustíveis presentes no stock. */
function distinct_fuels(): array
{
    return db()->query("SELECT DISTINCT fuel FROM vehicles WHERE fuel <> '' ORDER BY fuel")
        ->fetchAll(PDO::FETCH_COLUMN);
}

/** Contagens para o dashboard. */
function admin_counts(): array
{
    return [
        'vehicles'   => (int) db()->query('SELECT COUNT(*) FROM vehicles')->fetchColumn(),
        'available'  => (int) db()->query("SELECT COUNT(*) FROM vehicles WHERE status='disponivel'")->fetchColumn(),
        'brands'     => (int) db()->query('SELECT COUNT(*) FROM brands')->fetchColumn(),
        'categories' => (int) db()->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
        'leads'      => (int) db()->query('SELECT COUNT(*) FROM leads WHERE is_read=0')->fetchColumn(),
    ];
}
