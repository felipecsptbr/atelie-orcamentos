<?php
/**
 * Listagem de Clientes
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Clientes';
$mensagem = '';
$tipo_mensagem = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    try {
        $db = getDB();
        
        if ($acao === 'excluir') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                $sql = "UPDATE clientes SET ativo = 0 WHERE id = ?";
                $db->execute($sql, [$id]);
                $mensagem = 'Cliente excluído com sucesso!';
                $tipo_mensagem = 'success';
            }
        } elseif ($acao === 'salvar') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $nome = trim($_POST['nome'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $whatsapp = trim($_POST['whatsapp'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $endereco = trim($_POST['endereco'] ?? '');
            $observacoes = trim($_POST['observacoes'] ?? '');
            
            if (empty($nome)) {
                throw new Exception('Nome do cliente é obrigatório');
            }
            if (empty($telefone)) {
                throw new Exception('Telefone é obrigatório');
            }
            
            if ($id) {
                // Atualizar
                $sql = "UPDATE clientes SET nome = ?, telefone = ?, whatsapp = ?, email = ?, endereco = ?, observacoes = ? WHERE id = ?";
                $db->execute($sql, [$nome, $telefone, $whatsapp, $email, $endereco, $observacoes, $id]);
                $mensagem = 'Cliente atualizado com sucesso!';
            } else {
                // Inserir
                $sql = "INSERT INTO clientes (nome, telefone, whatsapp, email, endereco, observacoes) VALUES (?, ?, ?, ?, ?, ?)";
                $db->execute($sql, [$nome, $telefone, $whatsapp, $email, $endereco, $observacoes]);
                $mensagem = 'Cliente cadastrado com sucesso!';
            }
            $tipo_mensagem = 'success';
        }
    } catch (Exception $e) {
        $mensagem = 'Erro: ' . $e->getMessage();
        $tipo_mensagem = 'danger';
    }
}

// Buscar clientes
try {
    $db = getDB();
    $sql = "SELECT c.*, 
            (SELECT COUNT(*) FROM orcamentos WHERE cliente_id = c.id) as total_orcamentos,
            (SELECT SUM(total) FROM orcamentos WHERE cliente_id = c.id) as valor_total
            FROM clientes c 
            WHERE c.ativo = 1 
            ORDER BY c.nome";
    $clientes = $db->query($sql);
} catch (Exception $e) {
    $mensagem = 'Erro ao carregar clientes: ' . $e->getMessage();
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
                    <h1 class="m-0">Clientes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Clientes</li>
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
                    <h3 class="card-title">Lista de Clientes</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCliente">
                            <i class="fas fa-plus"></i> Novo Cliente
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tableClientes" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>Email</th>
                                <th>Orçamentos</th>
                                <th>Valor Total</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($clientes)): ?>
                                <?php foreach ($clientes as $cliente): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($cliente['nome']); ?></strong>
                                            <?php if ($cliente['whatsapp']): ?>
                                                <br><small class="text-success">
                                                    <i class="fab fa-whatsapp"></i> <?php echo htmlspecialchars($cliente['whatsapp']); ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($cliente['telefone']); ?></td>
                                        <td><?php echo htmlspecialchars($cliente['email'] ?? '-'); ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-info"><?php echo $cliente['total_orcamentos']; ?></span>
                                        </td>
                                        <td>R$ <?php echo number_format($cliente['valor_total'] ?? 0, 2, ',', '.'); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info btn-historico" 
                                                    data-id="<?php echo $cliente['id']; ?>"
                                                    data-nome="<?php echo htmlspecialchars($cliente['nome']); ?>"
                                                    title="Histórico">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning btn-editar" 
                                                    data-id="<?php echo $cliente['id']; ?>"
                                                    data-nome="<?php echo htmlspecialchars($cliente['nome']); ?>"
                                                    data-telefone="<?php echo htmlspecialchars($cliente['telefone']); ?>"
                                                    data-whatsapp="<?php echo htmlspecialchars($cliente['whatsapp']); ?>"
                                                    data-email="<?php echo htmlspecialchars($cliente['email']); ?>"
                                                    data-endereco="<?php echo htmlspecialchars($cliente['endereco']); ?>"
                                                    data-observacoes="<?php echo htmlspecialchars($cliente['observacoes']); ?>"
                                                    title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="acao" value="excluir">
                                                <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger btn-delete" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </section>
</div>

<!-- Modal Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" id="formCliente">
                <input type="hidden" name="acao" value="salvar">
                <input type="hidden" name="id" id="cliente_id">
                
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Cadastro de Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nome Completo *</label>
                        <input type="text" class="form-control" name="nome" id="cliente_nome" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Telefone *</label>
                                <input type="text" class="form-control telefone" name="telefone" id="cliente_telefone" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>WhatsApp</label>
                                <input type="text" class="form-control celular" name="whatsapp" id="cliente_whatsapp">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" id="cliente_email">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Endereço</label>
                        <input type="text" class="form-control" name="endereco" id="cliente_endereco">
                    </div>
                    
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea class="form-control" name="observacoes" id="cliente_observacoes" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Histórico -->
<div class="modal fade" id="modalHistorico" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">Histórico de Orçamentos - <span id="historico_cliente_nome"></span></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="historico_conteudo">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-3x"></i>
                        <p>Carregando histórico...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?php
$extraJS = "
<script>
$(document).ready(function() {
    // DataTable
    $('#tableClientes').DataTable();
    
    // Limpar modal ao fechar
    $('#modalCliente').on('hidden.bs.modal', function() {
        $('#formCliente')[0].reset();
        $('#cliente_id').val('');
        $('.modal-title').text('Cadastro de Cliente');
    });
    
    // Editar cliente
    $('.btn-editar').on('click', function() {
        $('#cliente_id').val($(this).data('id'));
        $('#cliente_nome').val($(this).data('nome'));
        $('#cliente_telefone').val($(this).data('telefone'));
        $('#cliente_whatsapp').val($(this).data('whatsapp'));
        $('#cliente_email').val($(this).data('email'));
        $('#cliente_endereco').val($(this).data('endereco'));
        $('#cliente_observacoes').val($(this).data('observacoes'));
        $('.modal-title').text('Editar Cliente');
        $('#modalCliente').modal('show');
    });
    
    // Histórico do cliente
    $('.btn-historico').on('click', function() {
        var clienteId = $(this).data('id');
        var clienteNome = $(this).data('nome');
        
        $('#historico_cliente_nome').text(clienteNome);
        $('#modalHistorico').modal('show');
        
        // Carregar histórico via AJAX
        $.get('cliente_historico.php?id=' + clienteId, function(data) {
            $('#historico_conteudo').html(data);
        }).fail(function() {
            $('#historico_conteudo').html('<div class=\"alert alert-danger\">Erro ao carregar histórico.</div>');
        });
    });
});
</script>
";

include __DIR__ . '/includes/footer.php';
?>
