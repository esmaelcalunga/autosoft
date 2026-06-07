<?php
/* =====================================================================
 *  AutoSOFT — Autenticação do painel administrativo
 * ===================================================================== */

/** Cria o administrador por omissão se ainda não existir nenhum. */
function ensure_default_admin(): void
{
    $count = (int) db()->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
    if ($count > 0) {
        return;
    }
    $a = $GLOBALS['CONFIG']['default_admin'];
    $st = db()->prepare('INSERT INTO admin_users (name, email, password_hash) VALUES (?, ?, ?)');
    $st->execute([$a['name'], $a['email'], password_hash($a['pass'], PASSWORD_DEFAULT)]);
}

/** Tenta autenticar. Devolve true em caso de sucesso. */
function attempt_login(string $email, string $password): bool
{
    $st = db()->prepare('SELECT * FROM admin_users WHERE email = ? LIMIT 1');
    $st->execute([$email]);
    $user = $st->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin'] = [
            'id'    => (int) $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
        ];
        return true;
    }
    return false;
}

function current_admin(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function is_logged_in(): bool
{
    return isset($_SESSION['admin']);
}

/** Exige login; caso contrário redirecciona para o login. */
function require_login(): void
{
    if (!is_logged_in()) {
        redirect('/admin/login');
    }
}

function logout(): void
{
    unset($_SESSION['admin']);
}
