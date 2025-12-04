<?php
/**
 * Dashboard - Página Inicial
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Dashboard';

// Buscar estatísticas
try {
    $db = getDB();
    
    // Estatísticas do mês
    $stats = $db->querySingle("SELECT * FROM vw_estatisticas_mes");
    
    // Orçamentos recentes
    $sql = "SELECT o.*, c.nome as cliente_nome 
            FROM orcamentos o
            INNER JOIN clientes c ON o.cliente_id = c.id
            ORDER BY o.data_criacao DESC
            LIMIT 10";
    $orcamentos_recentes = $db->query($sql);
    
    // Dados para gráfico (últimos 6 meses)
    $sql = "SELECT 
                DATE_FORMAT(data_orcamento, '%Y-%m') as mes,
                COUNT(*) as total,
                SUM(total) as valor
            FROM orcamentos
            WHERE data_orcamento >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(data_orcamento, '%Y-%m')
            ORDER BY mes";
    $dados_grafico = $db->query($sql);
    
} catch (Exception $e) {
    $erro = "Erro ao carregar dados: " . $e->getMessage();
}

// Calcular taxa de conversão
$taxa_conversao = 0;
if ($stats && $stats['total_orcamentos'] > 0) {
    $taxa_conversao = ($stats['aprovados'] / $stats['total_orcamentos']) * 100;
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Home</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-ban"></i> <?php echo $erro; ?>
                </div>
            <?php endif; ?>
            
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $stats['total_orcamentos'] ?? 0; ?></h3>
                            <p>Orçamentos no Mês</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <a href="orcamentos.php" class="small-box-footer">Ver todos <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>R$ <?php echo number_format($stats['valor_total'] ?? 0, 2, ',', '.'); ?></h3>
                            <p>Valor Total</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <a href="orcamentos.php" class="small-box-footer">Ver detalhes <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo number_format($taxa_conversao, 1); ?>%</h3>
                            <p>Taxa de Aprovação</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="relatorios.php" class="small-box-footer">Ver relatórios <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo $stats['pendentes'] ?? 0; ?></h3>
                            <p>Orçamentos Pendentes</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="orcamentos.php?status=pendente" class="small-box-footer">Ver pendentes <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <!-- /.row -->

            <div class="row">
                <!-- Gráfico -->
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">Orçamentos - Últimos 6 Meses</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="chartOrcamentos" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Status dos Orçamentos -->
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">Status dos Orçamentos</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                                <p class="text-warning text-xl">
                                    <i class="fas fa-clock"></i>
                                </p>
                                <p class="d-flex flex-column text-right">
                                    <span class="font-weight-bold">Pendentes</span>
                                    <span class="text-muted"><?php echo $stats['pendentes'] ?? 0; ?> orçamentos</span>
                                </p>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                                <p class="text-success text-xl">
                                    <i class="fas fa-check-circle"></i>
                                </p>
                                <p class="d-flex flex-column text-right">
                                    <span class="font-weight-bold">Aprovados</span>
                                    <span class="text-muted"><?php echo $stats['aprovados'] ?? 0; ?> orçamentos</span>
                                </p>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                                <p class="text-info text-xl">
                                    <i class="fas fa-spinner"></i>
                                </p>
                                <p class="d-flex flex-column text-right">
                                    <span class="font-weight-bold">Em Execução</span>
                                    <span class="text-muted"><?php echo $stats['em_execucao'] ?? 0; ?> orçamentos</span>
                                </p>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                                <p class="text-secondary text-xl">
                                    <i class="fas fa-check-double"></i>
                                </p>
                                <p class="d-flex flex-column text-right">
                                    <span class="font-weight-bold">Concluídos</span>
                                    <span class="text-muted"><?php echo $stats['concluidos'] ?? 0; ?> orçamentos</span>
                                </p>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="text-danger text-xl">
                                    <i class="fas fa-times-circle"></i>
                                </p>
                                <p class="d-flex flex-column text-right">
                                    <span class="font-weight-bold">Cancelados</span>
                                    <span class="text-muted"><?php echo $stats['cancelados'] ?? 0; ?> orçamentos</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orçamentos Recentes -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Orçamentos Recentes</h3>
                            <div class="card-tools">
                                <a href="orcamento_novo.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Novo Orçamento
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Número</th>
                                        <th>Cliente</th>
                                        <th>Data</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($orcamentos_recentes)): ?>
                                        <?php foreach ($orcamentos_recentes as $orc): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($orc['numero']); ?></td>
                                                <td><?php echo htmlspecialchars($orc['cliente_nome']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($orc['data_orcamento'])); ?></td>
                                                <td>R$ <?php echo number_format($orc['total'], 2, ',', '.'); ?></td>
                                                <td>
                                                    <span class="badge badge-status-<?php echo $orc['status']; ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $orc['status'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="orcamento_visualizar.php?id=<?php echo $orc['id']; ?>" class="btn btn-sm btn-info" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="orcamento_pdf.php?id=<?php echo $orc['id']; ?>" class="btn btn-sm btn-danger" title="PDF" target="_blank">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Nenhum orçamento cadastrado</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->

<?php
$extraJS = "
<script>
// Gráfico de Orçamentos
$(document).ready(function() {
    var ctx = document.getElementById('chartOrcamentos').getContext('2d');
    
    var labels = [];
    var dataTotal = [];
    var dataValor = [];
    
    " . (isset($dados_grafico) ? "
    var dados = " . json_encode($dados_grafico) . ";
    dados.forEach(function(item) {
        var data = new Date(item.mes + '-01');
        labels.push(data.toLocaleDateString('pt-BR', { month: 'short', year: 'numeric' }));
        dataTotal.push(item.total);
        dataValor.push(parseFloat(item.valor));
    });
    " : "") . "
    
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Quantidade',
                data: dataTotal,
                borderColor: '#c06c84',
                backgroundColor: 'rgba(192, 108, 132, 0.1)',
                yAxisID: 'y'
            }, {
                label: 'Valor (R$)',
                data: dataValor,
                borderColor: '#6c5b7b',
                backgroundColor: 'rgba(108, 91, 123, 0.1)',
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Quantidade'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Valor (R$)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            }
        }
    });
});
</script>
";

include __DIR__ . '/includes/footer.php';
?>
