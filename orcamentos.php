<?php
/**
 * Listagem de Orçamentos
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Orçamentos';
$mensagem = '';
$tipo_mensagem = '';

// Filtros
$filtro_status = $_GET['status'] ?? '';
$filtro_busca = $_GET['busca'] ?? '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    try {
        $db = getDB();
        
        if ($acao === 'excluir') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                $sql = "DELETE FROM orcamentos WHERE id = ?";
                $db->execute($sql, [$id]);
                $mensagem = 'Orçamento excluído com sucesso!';
                $tipo_mensagem = 'success';
            }
        } elseif ($acao === 'mudar_status') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $status = $_POST['status'] ?? '';
            if ($id && in_array($status, ['pendente', 'aprovado', 'em_execucao', 'concluido', 'cancelado'])) {
                $sql = "UPDATE orcamentos SET status = ? WHERE id = ?";
                $db->execute($sql, [$status, $id]);
                $mensagem = 'Status atualizado com sucesso!';
                $tipo_mensagem = 'success';
            }
        }
    } catch (Exception $e) {
        $mensagem = 'Erro: ' . $e->getMessage();
        $tipo_mensagem = 'danger';
    }
}

// Buscar orçamentos
try {
    $db = getDB();
    $sql = "SELECT o.*, c.nome as cliente_nome, c.telefone as cliente_telefone
            FROM orcamentos o
            INNER JOIN clientes c ON o.cliente_id = c.id
            WHERE 1=1";
    $params = [];
    
    if ($filtro_status) {
        $sql .= " AND o.status = ?";
        $params[] = $filtro_status;
    }
    
    if ($filtro_busca) {
        $sql .= " AND (o.numero LIKE ? OR c.nome LIKE ?)";
        $params[] = "%$filtro_busca%";
        $params[] = "%$filtro_busca%";
    }
    
    $sql .= " ORDER BY o.data_orcamento DESC, o.id DESC";
    $orcamentos = $db->query($sql, $params);
    
} catch (Exception $e) {
    $mensagem = 'Erro ao carregar orçamentos: ' . $e->getMessage();
    $tipo_mensagem = 'danger';
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
                    <h1 class="m-0">Orçamentos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Orçamentos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Orçamentos</h3>
                    <div class="card-tools">
                        <a href="orcamento_novo.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Novo Orçamento
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <form method="get" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">Todos</option>
                                        <option value="pendente" <?php echo $filtro_status == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                        <option value="aprovado" <?php echo $filtro_status == 'aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                                        <option value="em_execucao" <?php echo $filtro_status == 'em_execucao' ? 'selected' : ''; ?>>Em Execução</option>
                                        <option value="concluido" <?php echo $filtro_status == 'concluido' ? 'selected' : ''; ?>>Concluído</option>
                                        <option value="cancelado" <?php echo $filtro_status == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Buscar</label>
                                    <input type="text" name="busca" class="form-control" placeholder="Número do orçamento ou nome do cliente" value="<?php echo htmlspecialchars($filtro_busca); ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Validade</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th width="180">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orcamentos)): ?>
                                    <?php foreach ($orcamentos as $orc): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($orc['numero']); ?></strong></td>
                                            <td>
                                                <?php echo htmlspecialchars($orc['cliente_nome']); ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($orc['cliente_telefone']); ?></small>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($orc['data_orcamento'])); ?></td>
                                            <td>
                                                <?php 
                                                $validade = date('d/m/Y', strtotime($orc['data_validade']));
                                                $hoje = date('Y-m-d');
                                                $vencido = $orc['data_validade'] < $hoje;
                                                ?>
                                                <span class="<?php echo $vencido ? 'text-danger' : ''; ?>">
                                                    <?php echo $validade; ?>
                                                    <?php if ($vencido): ?>
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    <?php endif; ?>
                                                </span>
                                            </td>
                                            <td><strong>R$ <?php echo number_format($orc['total'], 2, ',', '.'); ?></strong></td>
                                            <td>
                                                <select class="form-control form-control-sm status-select" data-id="<?php echo $orc['id']; ?>">
                                                    <option value="pendente" <?php echo $orc['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                                    <option value="aprovado" <?php echo $orc['status'] == 'aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                                                    <option value="em_execucao" <?php echo $orc['status'] == 'em_execucao' ? 'selected' : ''; ?>>Em Execução</option>
                                                    <option value="concluido" <?php echo $orc['status'] == 'concluido' ? 'selected' : ''; ?>>Concluído</option>
                                                    <option value="cancelado" <?php echo $orc['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                                </select>
                                            </td>
                                            <td>
                                                <a href="orcamento_visualizar.php?id=<?php echo $orc['id']; ?>" class="btn btn-sm btn-info" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="orcamento_novo.php?editar=<?php echo $orc['id']; ?>" class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="orcamento_novo.php?duplicar=<?php echo $orc['id']; ?>" class="btn btn-sm btn-secondary" title="Duplicar">
                                                    <i class="fas fa-copy"></i>
                                                </a>
                                                <a href="orcamento_pdf.php?id=<?php echo $orc['id']; ?>" class="btn btn-sm btn-danger" title="PDF" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <form method="post" style="display: inline;">
                                                    <input type="hidden" name="acao" value="excluir">
                                                    <input type="hidden" name="id" value="<?php echo $orc['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger btn-delete" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhum orçamento encontrado</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </section>
</div>

<?php
$extraJS = "
<script>
$(document).ready(function() {
    // Mudar status via AJAX
    $('.status-select').on('change', function() {
        var select = $(this);
        var orcamentoId = select.data('id');
        var novoStatus = select.val();
        
        $.post('', {
            acao: 'mudar_status',
            id: orcamentoId,
            status: novoStatus
        }, function(response) {
            // Atualizar a cor do badge
            select.removeClass().addClass('form-control form-control-sm status-select badge badge-status-' + novoStatus);
        });
    });
    
    // Adicionar classes de cor aos selects
    $('.status-select').each(function() {
        var status = $(this).val();
        $(this).addClass('badge badge-status-' + status);
    });
});
</script>
";

include __DIR__ . '/includes/footer.php';
?>
