<?php
require_once 'config.php';

$pageTitle = 'Detalhes do Orçamento - Ateliê Orçamentos';

// Função para formatar moeda
function formatCurrency($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

// Verificar ID do orçamento
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$orcamentoId = intval($_GET['id']);

try {
    $pdo = getConnection();
    
    // Buscar dados do orçamento
    $stmt = $pdo->prepare("
        SELECT o.*, c.nome as cliente_nome, c.telefone as cliente_telefone, 
               c.email as cliente_email, c.endereco as cliente_endereco
        FROM orcamentos o
        LEFT JOIN clientes c ON o.cliente_id = c.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orcamentoId]);
    $orcamento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$orcamento) {
        $_SESSION['error'] = 'Orçamento não encontrado';
        header('Location: index.php');
        exit;
    }
    
    // Buscar itens do orçamento
    $stmt = $pdo->prepare("
        SELECT * FROM orcamento_itens
        WHERE orcamento_id = ?
        ORDER BY id
    ");
    $stmt->execute([$orcamentoId]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $error = "Erro ao carregar orçamento: " . $e->getMessage();
}

ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-file-earmark-text text-primary"></i>
        Orçamento #<?php echo htmlspecialchars($orcamento['numero']); ?>
    </h1>
    <div class="btn-toolbar">
        <div class="btn-group me-2">
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <a href="gerar-pdf.php?id=<?php echo $orcamento['id']; ?>" class="btn btn-danger" target="_blank">
                <i class="bi bi-file-pdf"></i> Gerar PDF
            </a>
            <a href="gerar-pdf.php?id=<?php echo $orcamento['id']; ?>&preview=1" class="btn btn-info" target="_blank">
                <i class="bi bi-eye"></i> Preview
            </a>
        </div>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<!-- Status e Data -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card card-custom">
            <div class="card-body text-center">
                <i class="bi bi-calendar-check display-4 text-info mb-3"></i>
                <h6 class="text-muted mb-2">Data do Orçamento</h6>
                <h4><?php echo date('d/m/Y', strtotime($orcamento['data_orcamento'])); ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom">
            <div class="card-body text-center">
                <i class="bi bi-tag display-4 text-warning mb-3"></i>
                <h6 class="text-muted mb-2">Status</h6>
                <?php
                $statusColors = [
                    'pendente' => 'warning',
                    'aprovado' => 'success',
                    'concluido' => 'info',
                    'cancelado' => 'danger'
                ];
                $color = $statusColors[$orcamento['status']] ?? 'secondary';
                ?>
                <h4><span class="badge bg-<?php echo $color; ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $orcamento['status'])); ?>
                </span></h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom">
            <div class="card-body text-center">
                <i class="bi bi-currency-dollar display-4 text-success mb-3"></i>
                <h6 class="text-muted mb-2">Valor Total</h6>
                <h4 class="text-success"><?php echo formatCurrency($orcamento['valor_total']); ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- Dados do Cliente -->
<div class="card card-custom mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-person"></i> Dados do Cliente</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($orcamento['cliente_nome'] ?? 'Não informado'); ?></p>
                <p><strong>Telefone:</strong> <?php echo htmlspecialchars($orcamento['cliente_telefone'] ?? 'Não informado'); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>E-mail:</strong> <?php echo htmlspecialchars($orcamento['cliente_email'] ?? 'Não informado'); ?></p>
                <p><strong>Endereço:</strong> <?php echo htmlspecialchars($orcamento['cliente_endereco'] ?? 'Não informado'); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Serviços -->
<div class="card card-custom mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-tools"></i> Serviços</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 45%;">Descrição</th>
                        <th style="width: 15%; text-align: center;">Quantidade</th>
                        <th style="width: 15%; text-align: right;">Valor Unit.</th>
                        <th style="width: 20%; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $itemNum = 1;
                    foreach ($itens as $item): 
                    ?>
                    <tr>
                        <td><?php echo $itemNum++; ?></td>
                        <td><?php echo htmlspecialchars($item['descricao']); ?></td>
                        <td style="text-align: center;"><?php echo $item['quantidade']; ?></td>
                        <td style="text-align: right;"><?php echo formatCurrency($item['preco_unitario']); ?></td>
                        <td style="text-align: right;"><strong><?php echo formatCurrency($item['subtotal']); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right;"><strong>TOTAL:</strong></td>
                        <td style="text-align: right;">
                            <h5 class="text-success mb-0"><?php echo formatCurrency($orcamento['valor_total']); ?></h5>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Observações -->
<?php if (!empty($orcamento['observacoes'])): ?>
<div class="card card-custom mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Observações</h5>
    </div>
    <div class="card-body">
        <p class="mb-0"><?php echo nl2br(htmlspecialchars($orcamento['observacoes'])); ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Ações -->
<div class="card card-custom">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 text-center">
                <a href="gerar-pdf.php?id=<?php echo $orcamento['id']; ?>" class="btn btn-danger btn-lg me-2" target="_blank">
                    <i class="bi bi-file-pdf"></i> Baixar PDF
                </a>
                <a href="gerar-pdf.php?id=<?php echo $orcamento['id']; ?>&preview=1" class="btn btn-info btn-lg me-2" target="_blank">
                    <i class="bi bi-eye"></i> Visualizar Preview
                </a>
                <a href="index.php" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-arrow-left"></i> Voltar para Lista
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
