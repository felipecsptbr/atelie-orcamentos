<?php
/**
 * Relatórios
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Relatórios';

// Filtros
$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-t');
$exportar = $_GET['exportar'] ?? '';

try {
    $db = getDB();
    
    // Relatório de Orçamentos
    $sql = "SELECT o.*, c.nome as cliente_nome, c.telefone as cliente_telefone
            FROM orcamentos o
            INNER JOIN clientes c ON o.cliente_id = c.id
            WHERE o.data_orcamento BETWEEN ? AND ?
            ORDER BY o.data_orcamento DESC";
    $orcamentos = $db->query($sql, [$data_inicio, $data_fim]);
    
    // Estatísticas do período
    $stats = [
        'total' => count($orcamentos),
        'valor_total' => 0,
        'por_status' => [
            'pendente' => 0,
            'aprovado' => 0,
            'em_execucao' => 0,
            'concluido' => 0,
            'cancelado' => 0
        ]
    ];
    
    foreach ($orcamentos as $orc) {
        $stats['valor_total'] += $orc['total'];
        $stats['por_status'][$orc['status']]++;
    }
    
} catch (Exception $e) {
    $erro = 'Erro ao carregar relatórios: ' . $e->getMessage();
}

// Exportar para CSV
if ($exportar === 'csv' && !empty($orcamentos)) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="relatorio_orcamentos_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM para UTF-8
    
    // Cabeçalhos
    fputcsv($output, ['Número', 'Data', 'Cliente', 'Telefone', 'Valor', 'Status'], ';');
    
    // Dados
    foreach ($orcamentos as $orc) {
        fputcsv($output, [
            $orc['numero'],
            date('d/m/Y', strtotime($orc['data_orcamento'])),
            $orc['cliente_nome'],
            $orc['cliente_telefone'],
            'R$ ' . number_format($orc['total'], 2, ',', '.'),
            ucfirst(str_replace('_', ' ', $orc['status']))
        ], ';');
    }
    
    fclose($output);
    exit;
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Relatórios</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Relatórios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <!-- Filtros -->
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Filtros</h3>
                </div>
                <div class="card-body">
                    <form method="get" class="form-inline">
                        <div class="form-group mr-3">
                            <label class="mr-2">De:</label>
                            <input type="date" name="data_inicio" class="form-control" value="<?php echo $data_inicio; ?>">
                        </div>
                        <div class="form-group mr-3">
                            <label class="mr-2">Até:</label>
                            <input type="date" name="data_fim" class="form-control" value="<?php echo $data_fim; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="?data_inicio=<?php echo $data_inicio; ?>&data_fim=<?php echo $data_fim; ?>&exportar=csv" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Exportar CSV
                        </a>
                    </form>
                </div>
            </div>
            
            <!-- Estatísticas do Período -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $stats['total']; ?></h3>
                            <p>Total de Orçamentos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>R$ <?php echo number_format($stats['valor_total'], 2, ',', '.'); ?></h3>
                            <p>Valor Total</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo $stats['por_status']['aprovado']; ?></h3>
                            <p>Aprovados</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><?php echo $stats['por_status']['concluido']; ?></h3>
                            <p>Concluídos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status dos Orçamentos -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Orçamentos por Status</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chartStatus" height="200"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Resumo por Status</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th class="text-center">Quantidade</th>
                                        <th class="text-center">Percentual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['por_status'] as $status => $qtd): ?>
                                        <tr>
                                            <td>
                                                <span class="badge badge-status-<?php echo $status; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                                                </span>
                                            </td>
                                            <td class="text-center"><?php echo $qtd; ?></td>
                                            <td class="text-center">
                                                <?php echo $stats['total'] > 0 ? number_format(($qtd / $stats['total']) * 100, 1) : 0; ?>%
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Listagem de Orçamentos -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Orçamentos do Período</h3>
                </div>
                <div class="card-body table-responsive">
                    <table id="tableRelatorio" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orcamentos)): ?>
                                <?php foreach ($orcamentos as $orc): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($orc['numero']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($orc['data_orcamento'])); ?></td>
                                        <td><?php echo htmlspecialchars($orc['cliente_nome']); ?></td>
                                        <td>R$ <?php echo number_format($orc['total'], 2, ',', '.'); ?></td>
                                        <td>
                                            <span class="badge badge-status-<?php echo $orc['status']; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $orc['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="orcamento_visualizar.php?id=<?php echo $orc['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="orcamento_pdf.php?id=<?php echo $orc['id']; ?>" class="btn btn-sm btn-danger" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum orçamento encontrado no período</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </section>
</div>

<?php
$extraJS = "
<script>
$(document).ready(function() {
    $('#tableRelatorio').DataTable();
    
    // Gráfico de Status
    var ctx = document.getElementById('chartStatus').getContext('2d');
    var chartStatus = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pendente', 'Aprovado', 'Em Execução', 'Concluído', 'Cancelado'],
            datasets: [{
                data: [
                    " . $stats['por_status']['pendente'] . ",
                    " . $stats['por_status']['aprovado'] . ",
                    " . $stats['por_status']['em_execucao'] . ",
                    " . $stats['por_status']['concluido'] . ",
                    " . $stats['por_status']['cancelado'] . "
                ],
                backgroundColor: [
                    '#ffc107',
                    '#28a745',
                    '#17a2b8',
                    '#6c757d',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
";

include __DIR__ . '/includes/footer.php';
?>
