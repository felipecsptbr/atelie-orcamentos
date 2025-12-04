<?php
/**
 * Listagem de Serviços
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Serviços';
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
                $sql = "UPDATE servicos SET ativo = 0 WHERE id = ?";
                $db->execute($sql, [$id]);
                $mensagem = 'Serviço excluído com sucesso!';
                $tipo_mensagem = 'success';
            }
        } elseif ($acao === 'salvar') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $nome = trim($_POST['nome'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $preco_base = str_replace(['R$ ', '.', ','], ['', '', '.'], $_POST['preco_base'] ?? '0');
            $tempo_estimado = trim($_POST['tempo_estimado'] ?? '');
            $categoria = $_POST['categoria'] ?? 'outros';
            
            if (empty($nome)) {
                throw new Exception('Nome do serviço é obrigatório');
            }
            
            if ($id) {
                // Atualizar
                $sql = "UPDATE servicos SET nome = ?, descricao = ?, preco_base = ?, tempo_estimado = ?, categoria = ? WHERE id = ?";
                $db->execute($sql, [$nome, $descricao, $preco_base, $tempo_estimado, $categoria, $id]);
                $mensagem = 'Serviço atualizado com sucesso!';
            } else {
                // Inserir
                $sql = "INSERT INTO servicos (nome, descricao, preco_base, tempo_estimado, categoria) VALUES (?, ?, ?, ?, ?)";
                $db->execute($sql, [$nome, $descricao, $preco_base, $tempo_estimado, $categoria]);
                $mensagem = 'Serviço cadastrado com sucesso!';
            }
            $tipo_mensagem = 'success';
        }
    } catch (Exception $e) {
        $mensagem = 'Erro: ' . $e->getMessage();
        $tipo_mensagem = 'danger';
    }
}

// Buscar serviços
try {
    $db = getDB();
    $sql = "SELECT * FROM servicos WHERE ativo = 1 ORDER BY categoria, nome";
    $servicos = $db->query($sql);
} catch (Exception $e) {
    $mensagem = 'Erro ao carregar serviços: ' . $e->getMessage();
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
                    <h1 class="m-0">Serviços</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Serviços</li>
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
                    <h3 class="card-title">Lista de Serviços</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalServico">
                            <i class="fas fa-plus"></i> Novo Serviço
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tableServicos" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Preço Base</th>
                                <th>Tempo Estimado</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($servicos)): ?>
                                <?php foreach ($servicos as $servico): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($servico['nome']); ?></strong>
                                            <?php if ($servico['descricao']): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($servico['descricao']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?php echo ucfirst($servico['categoria']); ?>
                                            </span>
                                        </td>
                                        <td>R$ <?php echo number_format($servico['preco_base'], 2, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars($servico['tempo_estimado'] ?? '-'); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning btn-editar" 
                                                    data-id="<?php echo $servico['id']; ?>"
                                                    data-nome="<?php echo htmlspecialchars($servico['nome']); ?>"
                                                    data-descricao="<?php echo htmlspecialchars($servico['descricao']); ?>"
                                                    data-preco="<?php echo $servico['preco_base']; ?>"
                                                    data-tempo="<?php echo htmlspecialchars($servico['tempo_estimado']); ?>"
                                                    data-categoria="<?php echo $servico['categoria']; ?>"
                                                    title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="acao" value="excluir">
                                                <input type="hidden" name="id" value="<?php echo $servico['id']; ?>">
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

<!-- Modal Serviço -->
<div class="modal fade" id="modalServico" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" id="formServico">
                <input type="hidden" name="acao" value="salvar">
                <input type="hidden" name="id" id="servico_id">
                
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Cadastro de Serviço</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nome do Serviço *</label>
                                <input type="text" class="form-control" name="nome" id="servico_nome" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Categoria *</label>
                                <select class="form-control" name="categoria" id="servico_categoria" required>
                                    <option value="ajustes">Ajustes</option>
                                    <option value="confeccoes">Confecções</option>
                                    <option value="consertos">Consertos</option>
                                    <option value="reformas">Reformas</option>
                                    <option value="outros">Outros</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea class="form-control" name="descricao" id="servico_descricao" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Preço Base *</label>
                                <input type="text" class="form-control dinheiro" name="preco_base" id="servico_preco" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tempo Estimado</label>
                                <input type="text" class="form-control" name="tempo_estimado" id="servico_tempo" placeholder="Ex: 2 dias úteis">
                            </div>
                        </div>
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

<?php
$extraJS = "
<script>
$(document).ready(function() {
    // DataTable
    $('#tableServicos').DataTable();
    
    // Limpar modal ao fechar
    $('#modalServico').on('hidden.bs.modal', function() {
        $('#formServico')[0].reset();
        $('#servico_id').val('');
        $('.modal-title').text('Cadastro de Serviço');
    });
    
    // Editar serviço
    $('.btn-editar').on('click', function() {
        $('#servico_id').val($(this).data('id'));
        $('#servico_nome').val($(this).data('nome'));
        $('#servico_descricao').val($(this).data('descricao'));
        $('#servico_preco').val('R$ ' + parseFloat($(this).data('preco')).toFixed(2).replace('.', ','));
        $('#servico_tempo').val($(this).data('tempo'));
        $('#servico_categoria').val($(this).data('categoria'));
        $('.modal-title').text('Editar Serviço');
        $('#modalServico').modal('show');
    });
});
</script>
";

include __DIR__ . '/includes/footer.php';
?>
