<?php
/**
 * Histórico de Orçamentos do Cliente (AJAX)
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$cliente_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$cliente_id) {
    echo '<div class="alert alert-danger">Cliente inválido</div>';
    exit;
}

try {
    $db = getDB();
    
    $sql = "SELECT * FROM orcamentos WHERE cliente_id = ? ORDER BY data_orcamento DESC";
    $orcamentos = $db->query($sql, [$cliente_id]);
    
    if (empty($orcamentos)) {
        echo '<div class="alert alert-info">Nenhum orçamento encontrado para este cliente.</div>';
    } else {
        echo '<table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($orcamentos as $orc) {
            echo '<tr>
                    <td>' . htmlspecialchars($orc['numero']) . '</td>
                    <td>' . date('d/m/Y', strtotime($orc['data_orcamento'])) . '</td>
                    <td>R$ ' . number_format($orc['total'], 2, ',', '.') . '</td>
                    <td><span class="badge badge-status-' . $orc['status'] . '">' . 
                        ucfirst(str_replace('_', ' ', $orc['status'])) . '</span></td>
                    <td>
                        <a href="orcamento_visualizar.php?id=' . $orc['id'] . '" class="btn btn-sm btn-info" target="_blank">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="orcamento_pdf.php?id=' . $orc['id'] . '" class="btn btn-sm btn-danger" target="_blank">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    </td>
                </tr>';
        }
        
        echo '</tbody></table>';
    }
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Erro ao carregar histórico: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
