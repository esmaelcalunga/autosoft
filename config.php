<?php
/* =====================================================================
 *  AutoSOFT — Configuração
 *  Edite as credenciais da base de dados conforme o seu servidor.
 * ===================================================================== */

return [
    // --- Base de dados ------------------------------------------------
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => '3306',
        'name'    => 'autosoft',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    // --- URL base -----------------------------------------------------
    // Deixe vazio se o site estiver na raiz do domínio (ex.: autosoft.ao).
    // Se estiver numa subpasta, indique-a, ex.: '/autosoft'
    'base_url' => '/autosoft',

    // --- Identidade ---------------------------------------------------
    'site_name' => 'AutoSOFT',
    'currency'  => 'Kz',

    // --- Credenciais por omissão do administrador ---------------------
    // Criadas automaticamente no primeiro acesso ao painel se a tabela
    // admin_users estiver vazia. ALTERE A SENHA após o primeiro login.
    'default_admin' => [
        'name'  => 'Administrador',
        'email' => 'admin@autosoft.ao',
        'pass'  => 'admin123',
    ],
];
