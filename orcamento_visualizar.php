<?php
/**
 * Visualizar Orçamento
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Visualizar Orçamento';
$sucesso = isset($_GET['sucesso']);

$orcamento_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$orcamento_id) {
    header('Location: orcamentos.php');
    exit;
}

// Carregar orçamento
try {
    $db = getDB();
    
    $sql = "SELECT o.*, c.nome as cliente_nome, c.telefone as cliente_telefone, 
            c.whatsapp as cliente_whatsapp, c.email as cliente_email, c.endereco as cliente_endereco,
            u.nome as usuario_nome
            FROM orcamentos o
            INNER JOIN clientes c ON o.cliente_id = c.id
            INNER JOIN usuarios u ON o.usuario_id = u.id
            WHERE o.id = ?";
    $orcamento = $db->querySingle($sql, [$orcamento_id]);
    
    if (!$orcamento) {
        header('Location: orcamentos.php');
        exit;
    }
    
    $itens = $db->query("SELECT i.*, s.nome as servico_nome 
                         FROM itens_orcamento i
                         INNER JOIN servicos s ON i.servico_id = s.id
                         WHERE i.orcamento_id = ?
                         ORDER BY i.ordem", [$orcamento_id]);
    
} catch (Exception $e) {
    die("Erro ao carregar orçamento: " . $e->getMessage());
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
                    <h1 class="m-0">Orçamento <?php echo htmlspecialchars($orcamento['numero']); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="orcamentos.php">Orçamentos</a></li>
                        <li class="breadcrumb-item active">Visualizar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php if ($sucesso): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-check"></i> Orçamento salvo com sucesso!
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">Detalhes do Orçamento</h3>
                            <div class="card-tools">
                                <a href="orcamento_pdf.php?id=<?php echo $orcamento['id']; ?>" class="btn btn-danger btn-sm" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Gerar PDF
                                </a>
                                <a href="orcamento_novo.php?editar=<?php echo $orcamento['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="orcamento_novo.php?duplicar=<?php echo $orcamento['id']; ?>" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-copy"></i> Duplicar
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-user"></i> Dados do Cliente</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="120">Nome:</th>
                                            <td><?php echo htmlspecialchars($orcamento['cliente_nome']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Telefone:</th>
                                            <td><?php echo htmlspecialchars($orcamento['cliente_telefone']); ?></td>
                                        </tr>
                                        <?php if ($orcamento['cliente_whatsapp']): ?>
                                        <tr>
                                            <th>WhatsApp:</th>
                                            <td>
                                                <a href="https://wa.me/55<?php echo preg_replace('/[^0-9]/', '', $orcamento['cliente_whatsapp']); ?>" target="_blank">
                                                    <i class="fab fa-whatsapp text-success"></i> <?php echo htmlspecialchars($orcamento['cliente_whatsapp']); ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if ($orcamento['cliente_email']): ?>
                                        <tr>
                                            <th>Email:</th>
                                            <td><?php echo htmlspecialchars($orcamento['cliente_email']); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-info-circle"></i> Informações do Orçamento</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="150">Número:</th>
                                            <td><?php echo htmlspecialchars($orcamento['numero']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Data:</th>
                                            <td><?php echo date('d/m/Y', strtotime($orcamento['data_orcamento'])); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Validade:</th>
                                            <td><?php echo date('d/m/Y', strtotime($orcamento['data_validade'])); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                <span class="badge badge-status-<?php echo $orcamento['status']; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $orcamento['status'])); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Criado por:</th>
                                            <td><?php echo htmlspecialchars($orcamento['usuario_nome']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h5><i class="fas fa-list"></i> Serviços Solicitados</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="40%">Descrição</th>
                                            <th width="15%">Quantidade</th>
                                            <th width="20%">Valor Unit.</th>
                                            <th width="20%">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $num = 1; ?>
                                        <?php foreach ($itens as $item): ?>
                                            <tr>
                                                <td><?php echo $num++; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($item['servico_nome']); ?></strong>
                                                    <?php if ($item['descricao']): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($item['descricao']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?php echo $item['quantidade']; ?></td>
                                                <td class="text-right">R$ <?php echo number_format($item['valor_unitario'], 2, ',', '.'); ?></td>
                                                <td class="text-right"><strong>R$ <?php echo number_format($item['valor_total'], 2, ',', '.'); ?></strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th colspan="4" class="text-right">Subtotal:</th>
                                            <th class="text-right">R$ <?php echo number_format($orcamento['subtotal'], 2, ',', '.'); ?></th>
                                        </tr>
                                        <?php if ($orcamento['desconto_valor'] > 0): ?>
                                        <tr>
                                            <th colspan="4" class="text-right">
                                                Desconto <?php echo $orcamento['desconto_tipo'] == 'percentual' ? '(' . number_format($orcamento['desconto_valor'], 2, ',', '.') . '%)' : ''; ?>:
                                            </th>
                                            <th class="text-right text-danger">
                                                - R$ <?php 
                                                if ($orcamento['desconto_tipo'] == 'percentual') {
                                                    echo number_format($orcamento['subtotal'] * ($orcamento['desconto_valor'] / 100), 2, ',', '.');
                                                } else {
                                                    echo number_format($orcamento['desconto_valor'], 2, ',', '.');
                                                }
                                                ?>
                                            </th>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <th colspan="4" class="text-right">TOTAL:</th>
                                            <th class="text-right text-success"><h4>R$ <?php echo number_format($orcamento['total'], 2, ',', '.'); ?></h4></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <?php if ($orcamento['prazo_execucao'] || $orcamento['forma_pagamento'] || $orcamento['observacoes']): ?>
                            <hr>
                            <div class="row">
                                <?php if ($orcamento['prazo_execucao']): ?>
                                <div class="col-md-6">
                                    <p><strong>Prazo de Execução:</strong> <?php echo htmlspecialchars($orcamento['prazo_execucao']); ?></p>
                                </div>
                                <?php endif; ?>
                                <?php if ($orcamento['forma_pagamento']): ?>
                                <div class="col-md-6">
                                    <p><strong>Forma de Pagamento:</strong> <?php echo htmlspecialchars($orcamento['forma_pagamento']); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($orcamento['observacoes']): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Observações:</strong><br><?php echo nl2br(htmlspecialchars($orcamento['observacoes'])); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <a href="orcamentos.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                            <a href="orcamento_pdf.php?id=<?php echo $orcamento['id']; ?>" class="btn btn-danger float-right" target="_blank">
                                <i class="fas fa-file-pdf"></i> Gerar PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
</div>

<?php
include __DIR__ . '/includes/footer.php';
?>
