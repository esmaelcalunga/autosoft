<?php
/* =====================================================================
 *  AutoSOFT — Funções auxiliares (helpers)
 * ===================================================================== */

/** Escapar para HTML. */
function e($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

/** URL base-aware. url('/estoque') => /subpasta/estoque */
function url(string $path = ''): string
{
    $base = rtrim($GLOBALS['CONFIG']['base_url'], '/');
    if ($path === '' || $path === '/') {
        return $base === '' ? '/' : $base . '/';
    }
    return $base . '/' . ltrim($path, '/');
}

/** URL de um asset (css, imagem...) com cache-busting via mtime. */
function asset(string $path): string
{
    $rel = ltrim($path, '/');
    $full = __DIR__ . '/../assets/' . $rel;
    $ver = is_file($full) ? filemtime($full) : null;
    return url('assets/' . $rel) . ($ver ? '?v=' . $ver : '');
}

/** URL de uma imagem carregada. */
function upload_url(string $path): string
{
    return url('uploads/' . ltrim($path, '/'));
}

/** 'image' ou 'video' a partir do path. */
function media_type(string $path): string
{
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return in_array($ext, ['mp4', 'webm', 'mov', 'ogg', 'ogv'], true) ? 'video' : 'image';
}

/** Formata um valor em Kwanza:  38500000 => "Kz 38.500.000". */
function kz($value): string
{
    $n = (int) $value;
    return $GLOBALS['CONFIG']['currency'] . ' ' . number_format($n, 0, ',', '.');
}

/** Gera um slug URL-amigável a partir de texto. */
function slugify(string $text): string
{
    $text = trim($text);
    if (function_exists('iconv')) {
        $conv = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($conv !== false) {
            $text = $conv;
        }
    }
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text === '' ? 'item' : $text;
}

/** Garante um slug único na coluna indicada (ignorando opcionalmente um id). */
function unique_slug(string $base, string $table, ?int $ignoreId = null): string
{
    $slug = $base;
    $i = 2;
    while (true) {
        $sql = "SELECT COUNT(*) FROM `$table` WHERE slug = ?";
        $params = [$slug];
        if ($ignoreId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $ignoreId;
        }
        $st = db()->prepare($sql);
        $st->execute($params);
        if ((int) $st->fetchColumn() === 0) {
            return $slug;
        }
        $slug = $base . '-' . $i;
        $i++;
    }
}

/** Redireccionar e terminar. */
function redirect(string $to): void
{
    header('Location: ' . (str_starts_with($to, 'http') ? $to : url($to)));
    exit;
}

/** Mensagem flash (sessão). */
function flash(string $msg, string $type = 'success'): void
{
    $_SESSION['flash'][] = ['msg' => $msg, 'type' => $type];
}
function get_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/** Token CSRF. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}
function csrf_check(): void
{
    $sent = $_POST['_csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(419);
        die('Sessão expirada ou pedido inválido (CSRF). Recarregue a página.');
    }
}

/** Renderiza uma view com layout. */
function render(string $view, array $data = [], string $layout = 'layout'): void
{
    extract($data, EXTR_SKIP);
    $viewFile = __DIR__ . '/../views/' . $view . '.php';
    ob_start();
    require $viewFile;
    $content = ob_get_clean();
    require __DIR__ . '/../views/' . $layout . '.php';
}

/** Renderiza uma view do painel admin. */
function render_admin(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $viewFile = __DIR__ . '/../views/admin/' . $view . '.php';
    ob_start();
    require $viewFile;
    $content = ob_get_clean();
    require __DIR__ . '/../views/admin/layout.php';
}

/** Lê um parâmetro GET com valor por omissão. */
function q(string $key, $default = '')
{
    return isset($_GET[$key]) ? trim((string) $_GET[$key]) : $default;
}

/** Badges (string separada por vírgulas) => array limpo. */
function parse_badges(?string $s): array
{
    if (!$s) {
        return [];
    }
    return array_values(array_filter(array_map('trim', explode(',', $s))));
}
