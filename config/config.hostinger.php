<?php
/**
 * Configuração para Hostinger
 * Este arquivo contém as configurações específicas para produção na Hostinger
 */

// IMPORTANTE: Preencha com os dados do seu banco MySQL da Hostinger
define('DB_HOST', 'localhost'); // Geralmente é 'localhost' na Hostinger
define('DB_NAME', 'u123456789_atelie'); // Substitua pelo nome do seu banco
define('DB_USER', 'u123456789_user'); // Substitua pelo usuário do banco
define('DB_PASS', 'SuaSenhaSegura123'); // Substitua pela senha do banco
define('DB_PORT', '3306'); // Porta padrão MySQL
define('DB_CHARSET', 'utf8mb4');

// URL do seu site na Hostinger
define('SITE_URL', 'https://seudominio.com'); // Substitua pelo seu domínio
define('SITE_NAME', 'Sistema de Orçamentos');
define('BASE_PATH', __DIR__);

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

// Configurações de Erro (PRODUÇÃO)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Para debug temporário, descomente as linhas abaixo:
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Criar diretórios necessários se não existirem
$dirs = [UPLOAD_PATH, PDF_LOGO_PATH, PDF_TEMP_PATH];
foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}
