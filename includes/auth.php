<?php
/**
 * Verificação de Autenticação
 * Incluir este arquivo em todas as páginas protegidas
 */

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Verifica timeout da sessão (2 horas)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > SESSION_LIFETIME)) {
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}

// Atualiza o tempo da sessão
$_SESSION['login_time'] = time();
