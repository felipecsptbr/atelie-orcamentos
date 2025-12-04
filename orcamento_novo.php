<?php
/**
 * Novo/Editar Orçamento
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Novo Orçamento';
$mensagem = '';
$tipo_mensagem = '';
$modo = 'novo'; // novo, editar, duplicar

// Verificar se está editando ou duplicando
$orcamento_id = null;
if (isset($_GET['editar'])) {
    $orcamento_id = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
    $modo = 'editar';
    $pageTitle = 'Editar Orçamento';
} elseif (isset($_GET['duplicar'])) {
    $orcamento_id = filter_input(INPUT_GET, 'duplicar', FILTER_VALIDATE_INT);
    $modo = 'duplicar';
    $pageTitle = 'Duplicar Orçamento';
}

// Carregar dados para edição/duplicação
$orcamento = null;
$itens_orcamento = [];
if ($orcamento_id) {
    try {
        $db = getDB();
        $orcamento = $db->querySingle("SELECT * FROM orcamentos WHERE id = ?", [$orcamento_id]);
        if ($orcamento) {
            $itens_orcamento = $db->query("SELECT * FROM itens_orcamento WHERE orcamento_id = ? ORDER BY ordem", [$orcamento_id]);
        }
    } catch (Exception $e) {
        $mensagem = 'Erro ao carregar orçamento: ' . $e->getMessage();
        $tipo_mensagem = 'danger';
    }
}

// Processar salvamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar'])) {
    try {
        $db = getDB();
        
        // Dados do orçamento
        $cliente_id = filter_input(INPUT_POST, 'cliente_id', FILTER_VALIDATE_INT);
        $data_orcamento = $_POST['data_orcamento'] ?? date('Y-m-d');
        $data_validade = $_POST['data_validade'] ?? '';
        $desconto_tipo = $_POST['desconto_tipo'] ?? 'fixo';
        $desconto_valor = str_replace(['R$ ', '.', ','], ['', '', '.'], $_POST['desconto_valor'] ?? '0');
        $observacoes = trim($_POST['observacoes'] ?? '');
        $prazo_execucao = trim($_POST['prazo_execucao'] ?? '');
        $forma_pagamento = trim($_POST['forma_pagamento'] ?? '');
        $status = $_POST['status'] ?? 'pendente';
        
        // Itens
        $itens = $_POST['itens'] ?? [];
        
        if (!$cliente_id) {
            throw new Exception('Cliente é obrigatório');
        }
        if (empty($itens)) {
            throw new Exception('Adicione pelo menos um serviço');
        }
        
        // Calcular subtotal
        $subtotal = 0;
        foreach ($itens as $item) {
            $qtd = floatval($item['quantidade'] ?? 1);
            $valor = str_replace(['R$ ', '.', ','], ['', '', '.'], $item['valor_unitario'] ?? '0');
            $subtotal += $qtd * $valor;
        }
        
        // Calcular desconto
        $desconto_aplicado = 0;
        if ($desconto_tipo === 'percentual') {
            $desconto_aplicado = $subtotal * (floatval($desconto_valor) / 100);
        } else {
            $desconto_aplicado = floatval($desconto_valor);
        }
        
        $total = $subtotal - $desconto_aplicado;
        
        // Gerar número do orçamento
        $numero = '';
        if ($modo === 'editar' && $orcamento) {
            $numero = $orcamento['numero'];
        } else {
            $ano = date('Y');
            $ultimo = $db->querySingle("SELECT numero FROM orcamentos WHERE numero LIKE ? ORDER BY id DESC LIMIT 1", ["%/$ano"]);
            if ($ultimo) {
                $partes = explode('/', $ultimo['numero']);
                $seq = intval($partes[0]) + 1;
            } else {
                $seq = 1;
            }
            $numero = sprintf('%03d/%s', $seq, $ano);
        }
        
        $db->beginTransaction();
        
        if ($modo === 'editar' && $orcamento_id) {
            // Atualizar orçamento
            $sql = "UPDATE orcamentos SET 
                    cliente_id = ?, data_orcamento = ?, data_validade = ?,
                    subtotal = ?, desconto_tipo = ?, desconto_valor = ?, total = ?,
                    observacoes = ?, prazo_execucao = ?, forma_pagamento = ?, status = ?
                    WHERE id = ?";
            $db->execute($sql, [
                $cliente_id, $data_orcamento, $data_validade,
                $subtotal, $desconto_tipo, $desconto_valor, $total,
                $observacoes, $prazo_execucao, $forma_pagamento, $status,
                $orcamento_id
            ]);
            
            // Deletar itens antigos
            $db->execute("DELETE FROM itens_orcamento WHERE orcamento_id = ?", [$orcamento_id]);
            $novo_id = $orcamento_id;
            
        } else {
            // Inserir novo orçamento
            $sql = "INSERT INTO orcamentos 
                    (numero, cliente_id, data_orcamento, data_validade,
                     subtotal, desconto_tipo, desconto_valor, total,
                     observacoes, prazo_execucao, forma_pagamento, status, usuario_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $db->execute($sql, [
                $numero, $cliente_id, $data_orcamento, $data_validade,
                $subtotal, $desconto_tipo, $desconto_valor, $total,
                $observacoes, $prazo_execucao, $forma_pagamento, $status,
                $_SESSION['usuario_id']
            ]);
            $novo_id = $db->lastInsertId();
        }
        
        // Inserir itens
        $ordem = 0;
        foreach ($itens as $item) {
            $servico_id = intval($item['servico_id'] ?? 0);
            $descricao = trim($item['descricao'] ?? '');
            $quantidade = floatval($item['quantidade'] ?? 1);
            $valor_unitario = str_replace(['R$ ', '.', ','], ['', '', '.'], $item['valor_unitario'] ?? '0');
            $valor_total = $quantidade * $valor_unitario;
            
            $sql = "INSERT INTO itens_orcamento 
                    (orcamento_id, servico_id, descricao, quantidade, valor_unitario, valor_total, ordem)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $db->execute($sql, [
                $novo_id, $servico_id, $descricao, $quantidade, 
                $valor_unitario, $valor_total, $ordem++
            ]);
        }
        
        $db->commit();
        
        header('Location: orcamento_visualizar.php?id=' . $novo_id . '&sucesso=1');
        exit;
        
    } catch (Exception $e) {
        if (isset($db)) {
            $db->rollback();
        }
        $mensagem = 'Erro ao salvar orçamento: ' . $e->getMessage();
        $tipo_mensagem = 'danger';
    }
}

// Buscar clientes e serviços
try {
    $db = getDB();
    $clientes = $db->query("SELECT id, nome, telefone FROM clientes WHERE ativo = 1 ORDER BY nome");
    $servicos = $db->query("SELECT * FROM servicos WHERE ativo = 1 ORDER BY categoria, nome");
    $config = $db->querySingle("SELECT * FROM configuracoes LIMIT 1");
} catch (Exception $e) {
    $mensagem = 'Erro ao carregar dados: ' . $e->getMessage();
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
                    <h1 class="m-0"><?php echo $pageTitle; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="orcamentos.php">Orçamentos</a></li>
                        <li class="breadcrumb-item active"><?php echo $pageTitle; ?></li>
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
            
            <form method="post" id="formOrcamento">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Dados do Orçamento -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Dados do Orçamento</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cliente *</label>
                                            <select name="cliente_id" id="cliente_id" class="form-control select2" required style="width: 100%;">
                                                <option value="">Selecione um cliente</option>
                                                <?php foreach ($clientes as $cliente): ?>
                                                    <option value="<?php echo $cliente['id']; ?>" 
                                                            <?php echo ($orcamento && $orcamento['cliente_id'] == $cliente['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($cliente['nome']); ?> - <?php echo htmlspecialchars($cliente['telefone']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="form-text text-muted">
                                                <a href="#" data-toggle="modal" data-target="#modalClienteRapido">+ Cadastro rápido</a>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Data do Orçamento</label>
                                            <input type="date" class="form-control" name="data_orcamento" 
                                                   value="<?php echo $orcamento ? $orcamento['data_orcamento'] : date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Validade</label>
                                            <input type="date" class="form-control" name="data_validade" 
                                                   value="<?php echo $orcamento ? $orcamento['data_validade'] : date('Y-m-d', strtotime('+' . ($config['validade_padrao'] ?? 7) . ' days')); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Prazo de Execução</label>
                                            <input type="text" class="form-control" name="prazo_execucao" 
                                                   value="<?php echo $orcamento ? htmlspecialchars($orcamento['prazo_execucao']) : htmlspecialchars($config['prazo_execucao_padrao'] ?? ''); ?>"
                                                   placeholder="Ex: 3 dias úteis">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Forma de Pagamento</label>
                                            <input type="text" class="form-control" name="forma_pagamento" 
                                                   value="<?php echo $orcamento ? htmlspecialchars($orcamento['forma_pagamento']) : htmlspecialchars($config['forma_pagamento_padrao'] ?? ''); ?>"
                                                   placeholder="Ex: À vista, Pix, Cartão">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Observações</label>
                                    <textarea class="form-control" name="observacoes" rows="3" 
                                              placeholder="Observações adicionais sobre o orçamento"><?php echo $orcamento ? htmlspecialchars($orcamento['observacoes']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Itens do Orçamento -->
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Serviços</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-sm btn-light" id="btnAdicionarItem">
                                        <i class="fas fa-plus"></i> Adicionar Serviço
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tableItens">
                                        <thead>
                                            <tr>
                                                <th width="40%">Serviço</th>
                                                <th width="30%">Descrição/Detalhes</th>
                                                <th width="10%">Qtd</th>
                                                <th width="15%">Valor Unit.</th>
                                                <th width="15%">Total</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="itensContainer">
                                            <?php if (!empty($itens_orcamento)): ?>
                                                <?php foreach ($itens_orcamento as $idx => $item): ?>
                                                    <tr class="item-row">
                                                        <td>
                                                            <select name="itens[<?php echo $idx; ?>][servico_id]" class="form-control form-control-sm select-servico" required>
                                                                <option value="">Selecione...</option>
                                                                <?php foreach ($servicos as $servico): ?>
                                                                    <option value="<?php echo $servico['id']; ?>" 
                                                                            data-preco="<?php echo $servico['preco_base']; ?>"
                                                                            data-nome="<?php echo htmlspecialchars($servico['nome']); ?>"
                                                                            <?php echo $item['servico_id'] == $servico['id'] ? 'selected' : ''; ?>>
                                                                        <?php echo htmlspecialchars($servico['nome']); ?> - R$ <?php echo number_format($servico['preco_base'], 2, ',', '.'); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="itens[<?php echo $idx; ?>][descricao]" 
                                                                   class="form-control form-control-sm" 
                                                                   value="<?php echo htmlspecialchars($item['descricao']); ?>"
                                                                   placeholder="Detalhes adicionais">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="itens[<?php echo $idx; ?>][quantidade]" 
                                                                   class="form-control form-control-sm item-qtd" 
                                                                   value="<?php echo $item['quantidade']; ?>" min="1" step="1" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="itens[<?php echo $idx; ?>][valor_unitario]" 
                                                                   class="form-control form-control-sm dinheiro item-valor" 
                                                                   value="R$ <?php echo number_format($item['valor_unitario'], 2, ',', '.'); ?>" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm item-total" readonly value="R$ <?php echo number_format($item['valor_total'], 2, ',', '.'); ?>">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-danger btn-remover-item">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Resumo -->
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Resumo</h3>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th>Subtotal:</th>
                                        <td class="text-right"><strong id="subtotalDisplay">R$ 0,00</strong></td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <select name="desconto_tipo" id="desconto_tipo" class="form-control form-control-sm">
                                                <option value="fixo" <?php echo ($orcamento && $orcamento['desconto_tipo'] == 'fixo') ? 'selected' : ''; ?>>Desconto (R$)</option>
                                                <option value="percentual" <?php echo ($orcamento && $orcamento['desconto_tipo'] == 'percentual') ? 'selected' : ''; ?>>Desconto (%)</option>
                                            </select>
                                        </th>
                                        <td>
                                            <input type="text" name="desconto_valor" id="desconto_valor" 
                                                   class="form-control form-control-sm text-right dinheiro" 
                                                   value="<?php echo $orcamento ? 'R$ ' . number_format($orcamento['desconto_valor'], 2, ',', '.') : 'R$ 0,00'; ?>">
                                        </td>
                                    </tr>
                                    <tr class="bg-light">
                                        <th>TOTAL:</th>
                                        <td class="text-right"><h4 id="totalDisplay">R$ 0,00</h4></td>
                                    </tr>
                                </table>
                                
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="pendente" <?php echo ($orcamento && $orcamento['status'] == 'pendente') ? 'selected' : ''; ?>>Pendente</option>
                                        <option value="aprovado" <?php echo ($orcamento && $orcamento['status'] == 'aprovado') ? 'selected' : ''; ?>>Aprovado</option>
                                        <option value="em_execucao" <?php echo ($orcamento && $orcamento['status'] == 'em_execucao') ? 'selected' : ''; ?>>Em Execução</option>
                                        <option value="concluido" <?php echo ($orcamento && $orcamento['status'] == 'concluido') ? 'selected' : ''; ?>>Concluído</option>
                                        <option value="cancelado" <?php echo ($orcamento && $orcamento['status'] == 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                                    </select>
                                </div>
                                
                                <hr>
                                
                                <button type="submit" name="salvar" class="btn btn-primary btn-block">
                                    <i class="fas fa-save"></i> Salvar Orçamento
                                </button>
                                <a href="orcamentos.php" class="btn btn-secondary btn-block">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
        </div>
    </section>
</div>

<!-- Modal Cliente Rápido -->
<div class="modal fade" id="modalClienteRapido" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Cadastro Rápido de Cliente</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" class="form-control" id="quick_nome">
                </div>
                <div class="form-group">
                    <label>Telefone *</label>
                    <input type="text" class="form-control telefone" id="quick_telefone">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarClienteRapido">Salvar</button>
            </div>
        </div>
    </div>
</div>

<?php
$servicos_json = json_encode($servicos);

$extraJS = "
<script>
var servicosData = $servicos_json;
var itemIndex = " . (count($itens_orcamento) > 0 ? count($itens_orcamento) : 0) . ";

$(document).ready(function() {
    // Select2
    $('.select2').select2();
    
    // Adicionar item
    $('#btnAdicionarItem').on('click', function() {
        adicionarItem();
    });
    
    // Remover item
    $(document).on('click', '.btn-remover-item', function() {
        $(this).closest('tr').remove();
        calcularTotais();
    });
    
    // Quando selecionar um serviço
    $(document).on('change', '.select-servico', function() {
        var row = $(this).closest('tr');
        var preco = $(this).find(':selected').data('preco');
        var nome = $(this).find(':selected').data('nome');
        
        if (preco) {
            row.find('.item-valor').val('R$ ' + parseFloat(preco).toFixed(2).replace('.', ','));
            row.find('input[name*=\"[descricao]\"]').val(nome);
            calcularItemTotal(row);
        }
    });
    
    // Calcular quando mudar quantidade ou valor
    $(document).on('input', '.item-qtd, .item-valor', function() {
        var row = $(this).closest('tr');
        calcularItemTotal(row);
    });
    
    // Calcular quando mudar desconto
    $('#desconto_tipo, #desconto_valor').on('change input', function() {
        calcularTotais();
    });
    
    // Cadastro rápido de cliente
    $('#btnSalvarClienteRapido').on('click', function() {
        var nome = $('#quick_nome').val();
        var telefone = $('#quick_telefone').val();
        
        if (!nome || !telefone) {
            alert('Preencha nome e telefone');
            return;
        }
        
        $.post('cliente_rapido.php', {
            nome: nome,
            telefone: telefone
        }, function(response) {
            if (response.success) {
                var option = new Option(response.nome + ' - ' + response.telefone, response.id, true, true);
                $('#cliente_id').append(option).trigger('change');
                $('#modalClienteRapido').modal('hide');
                $('#quick_nome, #quick_telefone').val('');
            } else {
                alert('Erro ao cadastrar cliente: ' + response.erro);
            }
        }, 'json');
    });
    
    // Calcular totais iniciais
    calcularTotais();
});

function adicionarItem() {
    var html = '<tr class=\"item-row\">' +
        '<td>' +
            '<select name=\"itens[' + itemIndex + '][servico_id]\" class=\"form-control form-control-sm select-servico\" required>' +
                '<option value=\"\">Selecione...</option>';
    
    servicosData.forEach(function(servico) {
        html += '<option value=\"' + servico.id + '\" data-preco=\"' + servico.preco_base + '\" data-nome=\"' + servico.nome + '\">' +
                servico.nome + ' - R$ ' + parseFloat(servico.preco_base).toFixed(2).replace('.', ',') + '</option>';
    });
    
    html += '</select>' +
        '</td>' +
        '<td><input type=\"text\" name=\"itens[' + itemIndex + '][descricao]\" class=\"form-control form-control-sm\" placeholder=\"Detalhes adicionais\"></td>' +
        '<td><input type=\"number\" name=\"itens[' + itemIndex + '][quantidade]\" class=\"form-control form-control-sm item-qtd\" value=\"1\" min=\"1\" step=\"1\" required></td>' +
        '<td><input type=\"text\" name=\"itens[' + itemIndex + '][valor_unitario]\" class=\"form-control form-control-sm dinheiro item-valor\" value=\"R$ 0,00\" required></td>' +
        '<td><input type=\"text\" class=\"form-control form-control-sm item-total\" readonly value=\"R$ 0,00\"></td>' +
        '<td><button type=\"button\" class=\"btn btn-sm btn-danger btn-remover-item\"><i class=\"fas fa-trash\"></i></button></td>' +
    '</tr>';
    
    $('#itensContainer').append(html);
    
    // Aplicar máscara de dinheiro
    $('.dinheiro').inputmask('currency', {
        prefix: 'R$ ',
        radixPoint: ',',
        groupSeparator: '.',
        autoGroup: true,
        digits: 2,
        digitsOptional: false,
        placeholder: '0'
    });
    
    itemIndex++;
}

function calcularItemTotal(row) {
    var qtd = parseFloat(row.find('.item-qtd').val()) || 0;
    var valor = parseMoney(row.find('.item-valor').val());
    var total = qtd * valor;
    
    row.find('.item-total').val(formatMoney(total));
    calcularTotais();
}

function calcularTotais() {
    var subtotal = 0;
    
    $('.item-row').each(function() {
        var total = parseMoney($(this).find('.item-total').val());
        subtotal += total;
    });
    
    var desconto_tipo = $('#desconto_tipo').val();
    var desconto_valor = parseMoney($('#desconto_valor').val());
    var desconto_aplicado = 0;
    
    if (desconto_tipo === 'percentual') {
        desconto_aplicado = subtotal * (desconto_valor / 100);
    } else {
        desconto_aplicado = desconto_valor;
    }
    
    var total = subtotal - desconto_aplicado;
    
    $('#subtotalDisplay').text(formatMoney(subtotal));
    $('#totalDisplay').text(formatMoney(total));
}
</script>
";

include __DIR__ . '/includes/footer.php';
?>
