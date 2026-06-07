<?php
/* =====================================================================
 *  AutoSOFT — Painel administrativo (controlador + router)
 * ===================================================================== */

function admin_router(array $parts, string $method): void
{
    $action = $parts[0] ?? '';

    // Rotas públicas do painel (login)
    if ($action === 'login') {
        admin_login($method);
        return;
    }
    if ($action === 'logout') {
        logout();
        redirect('/admin/login');
    }

    // A partir daqui exige sessão iniciada
    require_login();

    switch ($action) {
        case '':
        case 'dashboard':
            admin_dashboard();
            break;

        case 'viaturas':
            $sub = $parts[1] ?? '';
            if ($sub === 'nova')        { admin_vehicle_form($method, null); }
            elseif ($sub === 'editar')  { admin_vehicle_form($method, (int)($parts[2] ?? 0)); }
            elseif ($sub === 'eliminar'){ admin_vehicle_delete((int)($parts[2] ?? 0)); }
            elseif ($sub === 'imagem' && ($parts[2] ?? '') === 'eliminar') { admin_image_delete((int)($parts[3] ?? 0)); }
            elseif ($sub === 'imagem' && ($parts[2] ?? '') === 'principal') { admin_image_make_main((int)($parts[3] ?? 0)); }
            else { admin_vehicles_list(); }
            break;

        case 'marcas':
            $sub = $parts[1] ?? '';
            if ($sub === 'eliminar') { admin_brand_delete((int)($parts[2] ?? 0)); }
            else { admin_brands($method); }
            break;

        case 'categorias':
            $sub = $parts[1] ?? '';
            if ($sub === 'eliminar') { admin_category_delete((int)($parts[2] ?? 0)); }
            else { admin_categories($method); }
            break;

        case 'leads':
            $sub = $parts[1] ?? '';
            if ($sub === 'eliminar') { admin_lead_delete((int)($parts[2] ?? 0)); }
            else { admin_leads(); }
            break;

        default:
            redirect('/admin');
    }
}

/* --------------------------------------------------------------------- */
/*  LOGIN                                                                 */
/* --------------------------------------------------------------------- */
function admin_login(string $method): void
{
    ensure_default_admin();

    if (is_logged_in()) {
        redirect('/admin');
    }

    $error = null;
    if ($method === 'POST') {
        csrf_check();
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        if (attempt_login($email, $pass)) {
            redirect('/admin');
        }
        $error = 'Credenciais inválidas. Tente novamente.';
    }

    // Layout próprio (sem chrome do painel)
    $title = 'Entrar — AutoSOFT Admin';
    $csrf  = csrf_field();
    require __DIR__ . '/../views/admin/login.php';
}

/* --------------------------------------------------------------------- */
/*  DASHBOARD                                                             */
/* --------------------------------------------------------------------- */
function admin_dashboard(): void
{
    $recent = find_vehicles(['limit' => 6]);
    render_admin('dashboard', [
        'title'  => 'Painel — AutoSOFT',
        'active' => 'dashboard',
        'counts' => admin_counts(),
        'recent' => $recent,
    ]);
}

/* --------------------------------------------------------------------- */
/*  VIATURAS                                                              */
/* --------------------------------------------------------------------- */
function admin_vehicles_list(): void
{
    $vehicles = find_vehicles([]);
    render_admin('vehicles_list', [
        'title'    => 'Viaturas — AutoSOFT Admin',
        'active'   => 'viaturas',
        'vehicles' => $vehicles,
    ]);
}

function admin_vehicle_form(string $method, ?int $id): void
{
    $vehicle = $id ? vehicle_by_id($id) : null;
    if ($id && !$vehicle) { flash('Viatura não encontrada.', 'error'); redirect('/admin/viaturas'); }

    if ($method === 'POST') {
        if (empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            $limit = ini_get('post_max_size');
            flash("Upload demasiado grande — excedeu o post_max_size do PHP ({$limit}). Reduza os ficheiros ou aumente esse limite em php.ini.", 'error');
            redirect($id ? "/admin/viaturas/editar/$id" : '/admin/viaturas/nova');
        }
        csrf_check();
        $brandId   = (int) ($_POST['brand_id'] ?? 0);
        $model     = trim($_POST['model'] ?? '');
        $version   = trim($_POST['version'] ?? '');

        if (!$brandId || $model === '') {
            flash('Marca e modelo são obrigatórios.', 'error');
            redirect($id ? "/admin/viaturas/editar/$id" : '/admin/viaturas/nova');
        }

        $brand = db()->query('SELECT name FROM brands WHERE id=' . $brandId)->fetchColumn();
        $slugBase = slugify(trim($brand . ' ' . $model . ' ' . $version));
        $slug = unique_slug($slugBase, 'vehicles', $id);

        $fields = [
            'brand_id'     => $brandId,
            'category_id'  => ($_POST['category_id'] ?? '') !== '' ? (int) $_POST['category_id'] : null,
            'model'        => $model,
            'version'      => $version,
            'slug'         => $slug,
            'year'         => trim($_POST['year'] ?? ''),
            'km'           => (int) preg_replace('/\D/', '', $_POST['km'] ?? '0'),
            'fuel'         => trim($_POST['fuel'] ?? ''),
            'transmission' => trim($_POST['transmission'] ?? ''),
            'power'        => trim($_POST['power'] ?? ''),
            'color'        => trim($_POST['color'] ?? ''),
            'price'        => (int) preg_replace('/\D/', '', $_POST['price'] ?? '0'),
            'installment'  => trim($_POST['installment'] ?? ''),
            'location'     => trim($_POST['location'] ?? ''),
            'condition'    => ($_POST['condition'] ?? 'seminova') === 'novo' ? 'novo' : 'seminova',
            'badges'       => trim($_POST['badges'] ?? ''),
            'description'  => trim($_POST['description'] ?? ''),
            'status'       => in_array($_POST['status'] ?? '', ['disponivel','reservado','vendido']) ? $_POST['status'] : 'disponivel',
            'featured'     => isset($_POST['featured']) ? 1 : 0,
        ];

        if ($id) {
            $set = implode(', ', array_map(fn($k) => "`$k` = :$k", array_keys($fields)));
            $st = db()->prepare("UPDATE vehicles SET $set WHERE id = :id");
            $st->execute($fields + ['id' => $id]);
            $vehId = $id;
        } else {
            $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($fields)));
            $ph   = implode(', ', array_map(fn($k) => ":$k", array_keys($fields)));
            $st = db()->prepare("INSERT INTO vehicles ($cols) VALUES ($ph)");
            $st->execute($fields);
            $vehId = (int) db()->lastInsertId();
        }

        handle_image_uploads($vehId);

        flash($id ? 'Viatura actualizada.' : 'Viatura criada.', 'success');
        redirect('/admin/viaturas/editar/' . $vehId);
    }

    render_admin('vehicle_form', [
        'title'      => ($id ? 'Editar' : 'Nova') . ' viatura — AutoSOFT Admin',
        'active'     => 'viaturas',
        'vehicle'    => $vehicle,
        'images'     => $id ? vehicle_images($id) : [],
        'brands'     => all_brands(),
        'categories' => all_categories(),
    ]);
}

/** Mensagem amigável para um código UPLOAD_ERR_*. */
function upload_err_msg(int $code): string
{
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:   return 'excede upload_max_filesize do PHP';
        case UPLOAD_ERR_FORM_SIZE:  return 'excede MAX_FILE_SIZE do formulário';
        case UPLOAD_ERR_PARTIAL:    return 'upload interrompido';
        case UPLOAD_ERR_NO_FILE:    return 'nenhum ficheiro';
        case UPLOAD_ERR_NO_TMP_DIR: return 'pasta temp do PHP em falta';
        case UPLOAD_ERR_CANT_WRITE: return 'PHP não conseguiu escrever no disco';
        case UPLOAD_ERR_EXTENSION:  return 'bloqueado por extensão PHP';
        default: return 'erro #' . $code;
    }
}

/** Guarda imagens/vídeos carregados (input multiple name="images[]"). */
function handle_image_uploads(int $vehicleId): void
{
    if (empty($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        return;
    }

    $dir = __DIR__ . '/../uploads';
    if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
        flash('Não foi possível criar a pasta uploads/. Crie-a manualmente e dê permissão de escrita (chmod 775).', 'error');
        return;
    }
    if (!is_writable($dir)) {
        flash('A pasta uploads/ existe mas não tem permissão de escrita. Execute: chmod -R 775 uploads', 'error');
        return;
    }

    $byMime = [
        'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp',
        'video/mp4'  => 'mp4', 'video/webm' => 'webm', 'video/quicktime' => 'mov',
    ];
    $byExt = [
        'jpg' => 'jpg', 'jpeg' => 'jpg', 'png' => 'png', 'webp' => 'webp',
        'mp4' => 'mp4', 'webm' => 'webm', 'mov' => 'mov',
    ];

    $maxSort = (int) db()->query("SELECT COALESCE(MAX(sort),0) FROM vehicle_images WHERE vehicle_id=$vehicleId")->fetchColumn();
    $ok = 0; $reasons = [];

    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        $orig = $_FILES['images']['name'][$i] ?? ('ficheiro #' . ($i + 1));
        $err  = $_FILES['images']['error'][$i] ?? UPLOAD_ERR_NO_FILE;

        if ($err !== UPLOAD_ERR_OK) {
            $reasons[] = $orig . ': ' . upload_err_msg($err);
            continue;
        }
        if (!is_uploaded_file($tmp)) {
            $reasons[] = $orig . ': não veio de um upload válido';
            continue;
        }

        $ext  = null;
        $mime = function_exists('mime_content_type') ? @mime_content_type($tmp) : '';
        if ($mime && isset($byMime[$mime])) {
            $ext = $byMime[$mime];
        } else {
            $extFromName = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
            if (isset($byExt[$extFromName])) { $ext = $byExt[$extFromName]; }
        }
        if (!$ext) {
            $reasons[] = $orig . ': formato não suportado (' . ($mime ?: 'mime desconhecido') . ')';
            continue;
        }

        $name = $vehicleId . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
        $dest = $dir . '/' . $name;
        if (!move_uploaded_file($tmp, $dest)) {
            $reasons[] = $orig . ': falha ao gravar (verifique permissões de uploads/)';
            continue;
        }

        $maxSort++;
        $st = db()->prepare('INSERT INTO vehicle_images (vehicle_id, path, sort) VALUES (?, ?, ?)');
        $st->execute([$vehicleId, $name, $maxSort]);
        $ok++;
    }

    if ($ok > 0)         { flash($ok . ' ficheiro(s) carregado(s) com sucesso.', 'success'); }
    if (!empty($reasons)){ flash('Falhas no upload — ' . implode(' • ', $reasons), 'error'); }
}

function admin_image_delete(int $imgId): void
{
    csrf_check();
    $st = db()->prepare('SELECT * FROM vehicle_images WHERE id = ?');
    $st->execute([$imgId]);
    $img = $st->fetch();
    if ($img) {
        @unlink(__DIR__ . '/../uploads/' . $img['path']);
        db()->prepare('DELETE FROM vehicle_images WHERE id = ?')->execute([$imgId]);
        flash('Imagem removida.', 'success');
        redirect('/admin/viaturas/editar/' . $img['vehicle_id']);
    }
    redirect('/admin/viaturas');
}

function admin_image_make_main(int $imgId): void
{
    csrf_check();
    $st = db()->prepare('SELECT * FROM vehicle_images WHERE id = ?');
    $st->execute([$imgId]);
    $img = $st->fetch();
    if ($img) {
        db()->prepare('UPDATE vehicle_images SET sort = sort + 1 WHERE vehicle_id = ?')
            ->execute([$img['vehicle_id']]);
        db()->prepare('UPDATE vehicle_images SET sort = 0 WHERE id = ?')->execute([$imgId]);
        flash('Imagem definida como principal.', 'success');
        redirect('/admin/viaturas/editar/' . $img['vehicle_id']);
    }
    redirect('/admin/viaturas');
}

function admin_vehicle_delete(int $id): void
{
    csrf_check();
    foreach (vehicle_images($id) as $img) {
        @unlink(__DIR__ . '/../uploads/' . $img['path']);
    }
    db()->prepare('DELETE FROM vehicles WHERE id = ?')->execute([$id]);
    flash('Viatura eliminada.', 'success');
    redirect('/admin/viaturas');
}

/* --------------------------------------------------------------------- */
/*  MARCAS                                                                */
/* --------------------------------------------------------------------- */
function admin_brands(string $method): void
{
    if ($method === 'POST') {
        csrf_check();
        $name = trim($_POST['name'] ?? '');
        if ($name !== '') {
            $slug = unique_slug(slugify($name), 'brands');
            db()->prepare('INSERT INTO brands (name, slug) VALUES (?, ?)')->execute([$name, $slug]);
            flash('Marca adicionada.', 'success');
        }
        redirect('/admin/marcas');
    }
    render_admin('brands', [
        'title'  => 'Marcas — AutoSOFT Admin',
        'active' => 'marcas',
        'brands' => db()->query('SELECT b.*, (SELECT COUNT(*) FROM vehicles v WHERE v.brand_id=b.id) AS n FROM brands b ORDER BY b.name')->fetchAll(),
    ]);
}

function admin_brand_delete(int $id): void
{
    csrf_check();
    db()->prepare('DELETE FROM brands WHERE id = ?')->execute([$id]);
    flash('Marca eliminada (e as suas viaturas).', 'success');
    redirect('/admin/marcas');
}

/* --------------------------------------------------------------------- */
/*  CATEGORIAS                                                            */
/* --------------------------------------------------------------------- */
function admin_categories(string $method): void
{
    if ($method === 'POST') {
        csrf_check();
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($name !== '') {
            $slug = unique_slug(slugify($name), 'categories');
            db()->prepare('INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)')
                ->execute([$name, $slug, $desc ?: null]);
            flash('Categoria adicionada.', 'success');
        }
        redirect('/admin/categorias');
    }
    render_admin('categories', [
        'title'      => 'Categorias — AutoSOFT Admin',
        'active'     => 'categorias',
        'categories' => db()->query('SELECT c.*, (SELECT COUNT(*) FROM vehicles v WHERE v.category_id=c.id) AS n FROM categories c ORDER BY c.name')->fetchAll(),
    ]);
}

function admin_category_delete(int $id): void
{
    csrf_check();
    db()->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
    flash('Categoria eliminada.', 'success');
    redirect('/admin/categorias');
}

/* --------------------------------------------------------------------- */
/*  LEADS                                                                 */
/* --------------------------------------------------------------------- */
function admin_leads(): void
{
    // marcar todos como lidos ao abrir
    db()->query('UPDATE leads SET is_read=1 WHERE is_read=0');
    $leads = db()->query(
        'SELECT l.*, v.model, v.slug AS vehicle_slug, b.name AS brand_name
         FROM leads l
         LEFT JOIN vehicles v ON v.id = l.vehicle_id
         LEFT JOIN brands b ON b.id = v.brand_id
         ORDER BY l.created_at DESC'
    )->fetchAll();
    render_admin('leads', [
        'title'  => 'Contactos — AutoSOFT Admin',
        'active' => 'leads',
        'leads'  => $leads,
    ]);
}

function admin_lead_delete(int $id): void
{
    csrf_check();
    db()->prepare('DELETE FROM leads WHERE id = ?')->execute([$id]);
    flash('Contacto eliminado.', 'success');
    redirect('/admin/leads');
}
