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

/** SELECT com JOIN de marca/categoria + imagem de capa + nº de contactos. */
function vehicle_select_base(): string
{
    return "SELECT v.*, b.name AS brand_name, b.slug AS brand_slug,
                   c.name AS category_name, c.slug AS category_slug,
                   (SELECT path FROM vehicle_images vi WHERE vi.vehicle_id = v.id
                    ORDER BY vi.sort, vi.id LIMIT 1) AS cover,
                   (SELECT COUNT(*) FROM leads l WHERE l.vehicle_id = v.id) AS contacts
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
        'views'      => (int) db()->query('SELECT COALESCE(SUM(views),0) FROM vehicles')->fetchColumn(),
        'favorites'  => (int) db()->query('SELECT COALESCE(SUM(favorites),0) FROM vehicles')->fetchColumn(),
        'leads_total'=> (int) db()->query('SELECT COUNT(*) FROM leads')->fetchColumn(),
    ];
}

function admin_top_viewed(int $limit = 5): array
{
    return db()->query(vehicle_select_base() . ' ORDER BY v.views DESC LIMIT ' . (int)$limit)->fetchAll();
}

function admin_top_favorited(int $limit = 5): array
{
    return db()->query(vehicle_select_base() . ' ORDER BY v.favorites DESC LIMIT ' . (int)$limit)->fetchAll();
}

function admin_status_breakdown(): array
{
    return db()->query("SELECT status, COUNT(*) AS n FROM vehicles GROUP BY status")->fetchAll();
}

function admin_brand_breakdown(): array
{
    return db()->query("SELECT b.name, COUNT(v.id) AS n FROM brands b
                        LEFT JOIN vehicles v ON v.brand_id = b.id
                        GROUP BY b.id, b.name HAVING n > 0 ORDER BY n DESC LIMIT 10")->fetchAll();
}

function admin_leads_last_30d(): array
{
    return db()->query("SELECT DATE(created_at) AS d, COUNT(*) AS n FROM leads
                        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
                        GROUP BY DATE(created_at) ORDER BY d")->fetchAll();
}

function admin_top_contacted(int $limit = 5): array
{
    return db()->query(vehicle_select_base() . ' HAVING contacts > 0 ORDER BY contacts DESC LIMIT ' . (int)$limit)->fetchAll();
}

/** Paginação genérica. */
function paginated(string $baseSelect, string $countSelect, array $where, array $params, array $sortable, string $sort, string $dir, int $page, int $perPage = 20): array
{
    $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
    $sortCol  = $sortable[$sort] ?? $sortable['_default'] ?? null;
    $dirSql   = (strtolower($dir) === 'asc') ? 'ASC' : 'DESC';
    $orderBy  = $sortCol ? ' ORDER BY ' . $sortCol . ' ' . $dirSql : '';
    $page     = max(1, $page);

    $cst = db()->prepare($countSelect . $whereSql);
    $cst->execute($params);
    $total = (int) $cst->fetchColumn();
    $pages = max(1, (int) ceil($total / $perPage));
    if ($page > $pages) { $page = $pages; }
    $offset = ($page - 1) * $perPage;

    $st = db()->prepare($baseSelect . $whereSql . $orderBy . ' LIMIT ' . (int)$perPage . ' OFFSET ' . (int)$offset);
    $st->execute($params);
    return ['items' => $st->fetchAll(), 'total' => $total, 'page' => $page, 'pages' => $pages, 'perPage' => $perPage];
}

function admin_vehicles_paginated(string $q, string $sort, string $dir, int $page): array
{
    $base  = vehicle_select_base();
    $count = "SELECT COUNT(*) FROM vehicles v JOIN brands b ON b.id = v.brand_id LEFT JOIN categories c ON c.id = v.category_id";
    $where = []; $params = [];
    if ($q !== '') {
        $where[] = '(v.model LIKE ? OR v.version LIKE ? OR b.name LIKE ?)';
        $like = '%' . $q . '%';
        array_push($params, $like, $like, $like);
    }
    return paginated($base, $count, $where, $params, [
        'model'     => 'v.model',
        'brand'     => 'b.name',
        'category'  => 'c.name',
        'year'      => 'v.year',
        'price'     => 'v.price',
        'status'    => 'v.status',
        'views'     => 'v.views',
        'favorites' => 'v.favorites',
        'contacts'  => 'contacts',
        'created'   => 'v.created_at',
        '_default'  => 'v.created_at',
    ], $sort, $dir, $page);
}

function admin_brands_paginated(string $q, string $sort, string $dir, int $page): array
{
    $base  = "SELECT b.*, (SELECT COUNT(*) FROM vehicles v WHERE v.brand_id = b.id) AS n FROM brands b";
    $count = "SELECT COUNT(*) FROM brands b";
    $where = []; $params = [];
    if ($q !== '') { $where[] = 'b.name LIKE ?'; $params[] = '%' . $q . '%'; }
    return paginated($base, $count, $where, $params, [
        'name' => 'b.name', 'n' => 'n', '_default' => 'b.name',
    ], $sort, $dir, $page);
}

function admin_categories_paginated(string $q, string $sort, string $dir, int $page): array
{
    $base  = "SELECT c.*, (SELECT COUNT(*) FROM vehicles v WHERE v.category_id = c.id) AS n FROM categories c";
    $count = "SELECT COUNT(*) FROM categories c";
    $where = []; $params = [];
    if ($q !== '') { $where[] = '(c.name LIKE ? OR c.description LIKE ?)'; $params[] = '%'.$q.'%'; $params[] = '%'.$q.'%'; }
    return paginated($base, $count, $where, $params, [
        'name' => 'c.name', 'n' => 'n', '_default' => 'c.name',
    ], $sort, $dir, $page);
}

function admin_leads_paginated(string $q, string $sort, string $dir, int $page): array
{
    $base = "SELECT l.*, v.model, v.slug AS vehicle_slug, b.name AS brand_name
             FROM leads l
             LEFT JOIN vehicles v ON v.id = l.vehicle_id
             LEFT JOIN brands b ON b.id = v.brand_id";
    $count = "SELECT COUNT(*) FROM leads l
              LEFT JOIN vehicles v ON v.id = l.vehicle_id
              LEFT JOIN brands b ON b.id = v.brand_id";
    $where = []; $params = [];
    if ($q !== '') {
        $where[] = '(l.name LIKE ? OR l.phone LIKE ? OR l.email LIKE ? OR l.message LIKE ? OR v.model LIKE ? OR b.name LIKE ?)';
        $like = '%' . $q . '%';
        for ($i = 0; $i < 6; $i++) { $params[] = $like; }
    }
    return paginated($base, $count, $where, $params, [
        'date'    => 'l.created_at',
        'name'    => 'l.name',
        'vehicle' => 'v.model',
        '_default'=> 'l.created_at',
    ], $sort, $dir, $page);
}
