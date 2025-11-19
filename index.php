<?php
require_once 'config.php';

$pageTitle = 'Ateliê Orçamentos - Sistema de Gestão';

// Função para formatar moeda
function formatCurrency($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

// Inicializar variáveis
$totalOrcamentos = 0;
$totalClientes = 0;
$valorTotal = 0;
$orcamentos = [];

try {
    $pdo = getConnection();
    
    // Buscar orçamentos recentes
    $stmt = $pdo->query("
        SELECT o.*, c.nome as cliente_nome, c.telefone as cliente_telefone,
               COUNT(i.id) as total_itens
        FROM orcamentos o 
        LEFT JOIN clientes c ON o.cliente_id = c.id 
        LEFT JOIN orcamento_itens i ON o.id = i.orcamento_id
        GROUP BY o.id 
        ORDER BY o.created_at DESC 
        LIMIT 10
    ");
    $orcamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Estatísticas
    $totalOrcamentos = $pdo->query("SELECT COUNT(*) FROM orcamentos")->fetchColumn();
    $totalClientes = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
    $valorTotal = $pdo->query("SELECT COALESCE(SUM(valor_total), 0) FROM orcamentos WHERE status != 'rejeitado'")->fetchColumn();
    
} catch(PDOException $e) {
    $error = "Erro ao carregar dados: " . $e->getMessage();
}

ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-house-door text-primary"></i>
        Dashboard - Ateliê Orçamentos
    </h1>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card card-custom text-center">
            <div class="card-body">
                <i class="bi bi-file-earmark-text display-4 text-primary mb-3"></i>
                <h3 class="text-primary"><?php echo $totalOrcamentos; ?></h3>
                <p class="text-muted mb-0">Total de Orçamentos</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom text-center">
            <div class="card-body">
                <i class="bi bi-people display-4 text-success mb-3"></i>
                <h3 class="text-success"><?php echo $totalClientes; ?></h3>
                <p class="text-muted mb-0">Clientes Cadastrados</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom text-center">
            <div class="card-body">
                <i class="bi bi-currency-dollar display-4 text-info mb-3"></i>
                <h3 class="text-info"><?php echo formatCurrency($valorTotal); ?></h3>
                <p class="text-muted mb-0">Valor Total</p>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card card-custom h-100">
            <div class="card-body text-center">
                <i class="bi bi-plus-circle display-1 text-primary mb-3"></i>
                <h4>Criar Novo Orçamento</h4>
                <p class="text-muted mb-3">
                    Crie orçamentos profissionais com múltiplos serviços de forma rápida e fácil
                </p>
                <a href="novo-orcamento.php" class="btn btn-primary btn-custom">
                    <i class="bi bi-plus"></i> Novo Orçamento
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-custom h-100">
            <div class="card-body text-center">
                <i class="bi bi-file-earmark-pdf display-1 text-danger mb-3"></i>
                <h4>Gerar Relatórios</h4>
                <p class="text-muted mb-3">
                    Visualize relatórios de orçamentos e clientes em formato PDF
                </p>
                <button class="btn btn-outline-danger btn-custom" onclick="alert('Funcionalidade em desenvolvimento!')">
                    <i class="bi bi-file-pdf"></i> Ver Relatórios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Orçamentos Recentes -->
<?php if (!empty($orcamentos)): ?>
<div class="card card-custom">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Orçamentos Recentes</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Telefone</th>
                        <th>Itens</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orcamentos as $orcamento): ?>
                    <tr>
                        <td><strong><?php echo $orcamento['numero']; ?></strong></td>
                        <td><?php echo htmlspecialchars($orcamento['cliente_nome']); ?></td>
                        <td><?php echo htmlspecialchars($orcamento['cliente_telefone']); ?></td>
                        <td>
                            <span class="badge bg-info"><?php echo $orcamento['total_itens']; ?> item(s)</span>
                        </td>
                        <td class="text-success fw-bold"><?php echo formatCurrency($orcamento['total']); ?></td>
                        <td>
                            <?php
                            $statusColors = [
                                'pendente' => 'warning',
                                'aprovado' => 'success',
                                'rejeitado' => 'danger',
                                'em_andamento' => 'info',
                                'concluido' => 'primary'
                            ];
                            $color = $statusColors[$orcamento['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $color; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $orcamento['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($orcamento['created_at'])); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="ver-orcamento.php?id=<?php echo $orcamento['id']; ?>" 
                                   class="btn btn-outline-primary" title="Ver Detalhes">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="gerar-pdf.php?id=<?php echo $orcamento['id']; ?>" 
                                   class="btn btn-outline-danger" title="Gerar PDF" target="_blank">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card card-custom">
    <div class="card-body text-center py-5">
        <i class="bi bi-inbox display-1 text-muted mb-3"></i>
        <h4>Nenhum orçamento encontrado</h4>
        <p class="text-muted mb-4">Comece criando seu primeiro orçamento</p>
        <a href="novo-orcamento.php" class="btn btn-primary btn-custom">
            <i class="bi bi-plus"></i> Criar Primeiro Orçamento
        </a>
    </div>
</div>
<?php endif; ?>

<!-- Funcionalidades do Sistema -->
<div class="mt-5">
    <div class="card card-custom">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-star"></i> Funcionalidades do Sistema</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Criação de orçamentos com múltiplos serviços
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Biblioteca de serviços pré-definidos
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Geração automática de PDF profissional
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Interface moderna e responsiva
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Cadastro automático de clientes
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Cálculo automático de totais
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Sistema de desconto flexível
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Controle de status dos orçamentos
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>