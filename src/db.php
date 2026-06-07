<?php
/* =====================================================================
 *  AutoSOFT — Ligação à base de dados (PDO, singleton)
 * ===================================================================== */

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $cfg = $GLOBALS['CONFIG']['db'];
    $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['name']};charset={$cfg['charset']}";

    try {
        $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        die('Erro de ligação à base de dados: ' . htmlspecialchars($e->getMessage())
            . '<br>Verifique as credenciais em <code>config.php</code> e se importou <code>database/schema.sql</code>.');
    }

    ensure_schema($pdo);
    return $pdo;
}

/** Adiciona colunas em falta sem precisar de migração manual. */
function ensure_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) return;
    $done = true;
    try {
        $cols = $pdo->query("SHOW COLUMNS FROM vehicles")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('views', $cols, true)) {
            $pdo->exec("ALTER TABLE vehicles ADD COLUMN views INT UNSIGNED NOT NULL DEFAULT 0");
        }
        if (!in_array('favorites', $cols, true)) {
            $pdo->exec("ALTER TABLE vehicles ADD COLUMN favorites INT UNSIGNED NOT NULL DEFAULT 0");
        }
    } catch (Throwable $e) {}
}
