<?php
/**
 * Cadastro Rápido de Cliente (AJAX)
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');

$nome = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');

if (empty($nome) || empty($telefone)) {
    echo json_encode(['success' => false, 'erro' => 'Nome e telefone são obrigatórios']);
    exit;
}

try {
    $db = getDB();
    $sql = "INSERT INTO clientes (nome, telefone, whatsapp) VALUES (?, ?, ?)";
    $db->execute($sql, [$nome, $telefone, $telefone]);
    $id = $db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'id' => $id,
        'nome' => $nome,
        'telefone' => $telefone
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'erro' => $e->getMessage()]);
}
?>
