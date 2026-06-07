<?php
/* =====================================================================
 *  AutoSOFT — Front controller / Router
 *  Encaminha tanto o site público como o painel /admin.
 * ===================================================================== */

session_start();
mb_internal_encoding('UTF-8');

$CONFIG = require __DIR__ . '/config.php';
$GLOBALS['CONFIG'] = $CONFIG;

require __DIR__ . '/src/db.php';
require __DIR__ . '/src/helpers.php';
require __DIR__ . '/src/auth.php';
require __DIR__ . '/src/models.php';
require __DIR__ . '/src/components.php';
require __DIR__ . '/controllers/site.php';
require __DIR__ . '/controllers/admin.php';

/* --- Determinar o caminho do pedido, ignorando a base_url ------------ */
$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$base = rtrim($CONFIG['base_url'], '/');
if ($base !== '' && str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}
$path  = trim($uri, '/');
$parts = $path === '' ? [] : explode('/', $path);
$method = $_SERVER['REQUEST_METHOD'];

/* --- Painel administrativo ------------------------------------------- */
if (($parts[0] ?? '') === 'admin') {
    admin_router(array_slice($parts, 1), $method);
    exit;
}

/* --- Site público ---------------------------------------------------- */
try {
    switch ($parts[0] ?? '') {
        case '':
            page_home();
            break;

        case 'estoque':
        case 'catalogo':
            page_catalog();
            break;

        case 'marca':
            if (empty($parts[1])) { redirect('/estoque'); }
            page_brand($parts[1]);
            break;

        case 'categoria':
            if (empty($parts[1])) { redirect('/estoque'); }
            page_category($parts[1]);
            break;

        case 'viatura':
            if (empty($parts[1])) { redirect('/estoque'); }
            if ($method === 'POST') {
                page_vehicle_lead($parts[1]);
            } else {
                page_vehicle($parts[1]);
            }
            break;

        case 'vender':
            page_sell();
            break;

        case 'sobre':
            page_about();
            break;

        default:
            page_not_found();
    }
} catch (Throwable $ex) {
    http_response_code(500);
    echo '<pre style="padding:24px;font-family:monospace">Erro: '
        . e($ex->getMessage()) . "\n" . e($ex->getFile()) . ':' . $ex->getLine() . '</pre>';
}
