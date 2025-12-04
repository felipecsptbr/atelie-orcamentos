<?php
/**
 * Arquivo de Configuração do Sistema
 * Ateliê de Costura - Sistema de Orçamentos
 */

// Função helper para ler variáveis de ambiente
function env($key, $default = null) {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    return $value !== false ? $value : $default;
}

// Detectar ambiente (Railway, Render, ou local)
$is_railway = env('RAILWAY_ENVIRONMENT') !== null;
$is_render = env('RENDER') !== null;

// Configurações do Banco de Dados
if ($is_railway) {
    // Railway: Usa variáveis de ambiente do MySQL
    define('DB_HOST', env('MYSQLHOST', 'localhost'));
    define('DB_PORT', env('MYSQLPORT', '3306'));
    define('DB_NAME', env('MYSQLDATABASE', 'railway'));
    define('DB_USER', env('MYSQLUSER', 'root'));
    define('DB_PASS', env('MYSQLPASSWORD', ''));
    define('SITE_URL', 'https://' . env('RAILWAY_PUBLIC_DOMAIN'));
} elseif ($is_render) {
    // Render: Usa PostgreSQL ou MySQL externo
    define('DB_HOST', env('DB_HOST', 'localhost'));
    define('DB_NAME', env('DB_NAME', 'atelie_orcamentos'));
    define('DB_USER', env('DB_USER', 'root'));
    define('DB_PASS', env('DB_PASS', ''));
    define('SITE_URL', env('RENDER_EXTERNAL_URL'));
} else {
    // Local: XAMPP
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'atelie_orcamentos');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('SITE_URL', 'http://localhost/atelie-orcamentos');
}

define('DB_CHARSET', 'utf8mb4');

// Configurações do Sistema
define('SITE_NAME', 'Sistema de Orçamentos');
define('BASE_PATH', __DIR__ . '/..');

// Configurações de Sessão
define('SESSION_NAME', 'atelie_session');
define('SESSION_LIFETIME', 7200); // 2 horas

// Configurações de Upload
define('UPLOAD_PATH', BASE_PATH . '/uploads');
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Configurações de PDF
define('PDF_LOGO_PATH', UPLOAD_PATH . '/logo');
define('PDF_TEMP_PATH', BASE_PATH . '/temp');

// Configurações de Paginação
define('ITEMS_PER_PAGE', 15);

// Fuso Horário
date_default_timezone_set('America/Sao_Paulo');

// Configurações de Erro
if ($is_railway || $is_render) {
    // Produção: ocultar erros
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
} else {
    // Desenvolvimento: mostrar erros
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}
