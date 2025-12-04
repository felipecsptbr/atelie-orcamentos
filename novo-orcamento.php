<?php
require_once 'config.php';

$pageTitle = 'Novo Orçamento - Ateliê Orçamentos';

// Função para formatar moeda
function formatCurrency($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

// Função para gerar número do orçamento
function generateOrcamentoNumber() {
    return 'ORC-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

// Processar formulário
if ($_POST) {
    try {
        $pdo = getConnection();
        $pdo->beginTransaction();
        
        // Validar dados
        $clienteNome = trim($_POST['cliente_nome']);
        $clienteTelefone = trim($_POST['cliente_telefone']);
        $clienteEmail = trim($_POST['cliente_email']) ?: null;
        $dataVencimento = $_POST['data_vencimento'];
        $observacoes = trim($_POST['observacoes']) ?: null;
        $desconto = floatval($_POST['desconto']) ?: 0;
        $servicos = $_POST['servicos'] ?? [];
        
        if (empty($clienteNome) || empty($clienteTelefone) || empty($servicos)) {
            throw new Exception('Preencha todos os campos obrigatórios e adicione pelo menos um serviço.');
        }
        
        // Inserir ou buscar cliente
        $stmtCliente = $pdo->prepare("SELECT id FROM clientes WHERE telefone = ?");
        $stmtCliente->execute([$clienteTelefone]);
        $clienteId = $stmtCliente->fetchColumn();
        
        if (!$clienteId) {
            $stmtInsertCliente = $pdo->prepare("INSERT INTO clientes (nome, telefone, email) VALUES (?, ?, ?)");
            $stmtInsertCliente->execute([$clienteNome, $clienteTelefone, $clienteEmail]);
            $clienteId = $pdo->lastInsertId();
        } else {
            // Atualizar dados do cliente
            $stmtUpdateCliente = $pdo->prepare("UPDATE clientes SET nome = ?, email = ? WHERE id = ?");
            $stmtUpdateCliente->execute([$clienteNome, $clienteEmail, $clienteId]);
        }
        
        // Calcular total
        $subtotal = 0;
        foreach ($servicos as $servico) {
            $quantidade = intval($servico['quantidade']);
            $preco = floatval($servico['preco']);
            $subtotal += $quantidade * $preco;
        }
        $total = max(0, $subtotal - $desconto);
        
        // Inserir orçamento
        $numeroOrcamento = generateOrcamentoNumber();
        $stmtOrcamento = $pdo->prepare("
            INSERT INTO orcamentos (numero, cliente_id, data_orcamento, observacoes, valor_total) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmtOrcamento->execute([$numeroOrcamento, $clienteId, date('Y-m-d'), $observacoes, $total]);
        $orcamentoId = $pdo->lastInsertId();
        
        // Inserir itens do orçamento
        $stmtItem = $pdo->prepare("
            INSERT INTO orcamento_itens (orcamento_id, servico_id, descricao, quantidade, preco_unitario, subtotal) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($servicos as $servico) {
            $servicoId = intval($servico['id']);
            $quantidade = intval($servico['quantidade']);
            $preco = floatval($servico['preco']);
            $descricao = trim($servico['nome']);
            $subtotalItem = $quantidade * $preco;
            
            $stmtItem->execute([$orcamentoId, $servicoId, $descricao, $quantidade, $preco, $subtotalItem]);
        }
        
        $pdo->commit();
        $_SESSION['success'] = "Orçamento #{$numeroOrcamento} criado com sucesso!";
        header("Location: ver-orcamento.php?id={$orcamentoId}");
        exit;
        
    } catch(Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = $e->getMessage();
    }
}

// Buscar serviços disponíveis
try {
    $pdo = getConnection();
    $stmtServicos = $pdo->query("SELECT * FROM servicos ORDER BY nome");
    $servicos = $stmtServicos->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $servicos = [];
    $_SESSION['error'] = "Erro ao carregar serviços: " . $e->getMessage();
}

ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-plus-circle text-primary"></i>
        Novo Orçamento
    </h1>
    <div>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<form method="POST" id="orcamentoForm">
    <!-- Dados do Cliente -->
    <div class="card card-custom mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-person"></i> Dados do Cliente</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cliente_nome" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="cliente_nome" name="cliente_nome" 
                               value="<?php echo htmlspecialchars($_POST['cliente_nome'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cliente_telefone" class="form-label">Telefone *</label>
                        <input type="tel" class="form-control" id="cliente_telefone" name="cliente_telefone" 
                               value="<?php echo htmlspecialchars($_POST['cliente_telefone'] ?? ''); ?>" 
                               placeholder="(11) 99999-9999" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="cliente_email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="cliente_email" name="cliente_email" 
                               value="<?php echo htmlspecialchars($_POST['cliente_email'] ?? ''); ?>"
                               placeholder="cliente@email.com">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Serviços Disponíveis -->
    <div class="card card-custom mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-tools"></i> Serviços Disponíveis</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($servicos)): ?>
            <div class="row">
                <?php foreach ($servicos as $servico): ?>
                <div class="col-md-4 mb-3">
                    <div class="card servico-card h-100" 
                         data-id="<?php echo $servico['id']; ?>" 
                         data-nome="<?php echo htmlspecialchars($servico['nome']); ?>"
                         data-preco="<?php echo $servico['preco_base']; ?>">
                        <div class="card-body text-center">
                            <h6 class="card-title"><?php echo htmlspecialchars($servico['nome']); ?></h6>
                            <p class="card-text small text-muted">
                                <?php echo htmlspecialchars($servico['descricao']); ?>
                            </p>
                            <p class="text-success fw-bold mb-0">
                                <?php echo formatCurrency($servico['preco_base']); ?>
                            </p>
                            <button type="button" class="btn btn-outline-success btn-sm mt-2">
                                <i class="bi bi-plus"></i> Adicionar
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-4">
                <i class="bi bi-exclamation-triangle text-warning display-4"></i>
                <p class="mt-3">Nenhum serviço encontrado. Configure os serviços no sistema.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Itens do Orçamento -->
    <div class="card card-custom mb-4" id="itensCard" style="display: none;">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-list-check"></i> Itens do Orçamento</h5>
        </div>
        <div class="card-body" id="itensContainer">
            <!-- Itens serão adicionados aqui via JavaScript -->
        </div>
    </div>

    <!-- Configurações e Resumo -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-custom mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="bi bi-gear"></i> Configurações</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="data_vencimento" class="form-label">Validade do Orçamento *</label>
                        <input type="date" class="form-control" id="data_vencimento" name="data_vencimento" 
                               value="<?php echo $_POST['data_vencimento'] ?? date('Y-m-d', strtotime('+30 days')); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="desconto" class="form-label">Desconto (R$)</label>
                        <input type="number" class="form-control" id="desconto" name="desconto" 
                               step="0.01" min="0" value="<?php echo $_POST['desconto'] ?? 0; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações Gerais</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" 
                                  rows="4" placeholder="Observações sobre o orçamento..."><?php echo htmlspecialchars($_POST['observacoes'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card card-custom mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-calculator"></i> Resumo</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal" class="fw-bold">R$ 0,00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Desconto:</span>
                        <span id="descontoDisplay" class="text-danger">R$ 0,00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong id="total" class="text-success fs-5">R$ 0,00</strong>
                    </div>
                    
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <small>
                                Clique nos serviços acima para adicioná-los ao orçamento.
                                Você pode ajustar quantidades e valores depois.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botões de Ação -->
    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
        <button type="submit" class="btn btn-primary btn-custom" id="salvarBtn" disabled>
            <i class="bi bi-check-circle"></i> Salvar Orçamento
        </button>
    </div>
</form>

<?php
$content = ob_get_clean();

$scripts = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    let itensOrcamento = [];
    let contador = 0;

    // Adicionar serviço
    document.querySelectorAll(".servico-card").forEach(card => {
        card.addEventListener("click", function() {
            const id = this.dataset.id;
            const nome = this.dataset.nome;
            const preco = parseFloat(this.dataset.preco);

            // Verificar se já existe
            const existe = itensOrcamento.find(item => item.servico_id == id);
            if (existe) {
                existe.quantidade++;
            } else {
                adicionarItem(id, nome, preco);
            }
            renderizarItens();
            calcularTotal();
        });
    });

    function adicionarItem(servicoId, servicoNome, servicoPreco) {
        contador++;
        
        const item = {
            id: contador,
            servico_id: servicoId,
            nome: servicoNome,
            quantidade: 1,
            preco: servicoPreco
        };

        itensOrcamento.push(item);
    }

    function renderizarItens() {
        const container = document.getElementById("itensContainer");
        const card = document.getElementById("itensCard");

        if (itensOrcamento.length > 0) {
            card.style.display = "block";
            document.getElementById("salvarBtn").disabled = false;
        } else {
            card.style.display = "none";
            document.getElementById("salvarBtn").disabled = true;
        }

        container.innerHTML = "";

        itensOrcamento.forEach((item, index) => {
            const itemHtml = `
                <div class="card mb-3" id="item-${item.id}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <h6 class="mb-1">${item.nome}</h6>
                                <small class="text-muted">Serviço #${item.servico_id}</small>
                                <input type="hidden" name="servicos[${index}][id]" value="${item.servico_id}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantidade</label>
                                <input type="number" class="form-control quantidade" 
                                       name="servicos[${index}][quantidade]" 
                                       value="${item.quantidade}" min="1" data-index="${index}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Valor Unit.</label>
                                <input type="number" class="form-control preco" 
                                       name="servicos[${index}][preco]" 
                                       value="${item.preco}" step="0.01" min="0" data-index="${index}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Total</label>
                                <input type="text" class="form-control total-item" 
                                       value="R$ ${(item.quantidade * item.preco).toFixed(2).replace(".", ",")}" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" name="servicos[${index}][observacoes]" 
                                          rows="2" placeholder="Obs. específicas"></textarea>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-outline-danger btn-sm remover-item" 
                                            data-id="${item.id}" title="Remover">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML("beforeend", itemHtml);
        });

        adicionarEventListeners();
    }

    function adicionarEventListeners() {
        // Remover item
        document.querySelectorAll(".remover-item").forEach(btn => {
            btn.addEventListener("click", function() {
                const id = parseInt(this.dataset.id);
                itensOrcamento = itensOrcamento.filter(item => item.id !== id);
                renderizarItens();
                calcularTotal();
            });
        });

        // Atualizar quantidade
        document.querySelectorAll(".quantidade").forEach(input => {
            input.addEventListener("change", function() {
                const index = parseInt(this.dataset.index);
                const quantidade = parseInt(this.value) || 1;
                
                if (itensOrcamento[index]) {
                    itensOrcamento[index].quantidade = quantidade;
                    renderizarItens();
                    calcularTotal();
                }
            });
        });

        // Atualizar preço
        document.querySelectorAll(".preco").forEach(input => {
            input.addEventListener("change", function() {
                const index = parseInt(this.dataset.index);
                const preco = parseFloat(this.value) || 0;
                
                if (itensOrcamento[index]) {
                    itensOrcamento[index].preco = preco;
                    renderizarItens();
                    calcularTotal();
                }
            });
        });
    }

    function calcularTotal() {
        const subtotal = itensOrcamento.reduce((acc, item) => {
            return acc + (item.quantidade * item.preco);
        }, 0);

        const desconto = parseFloat(document.getElementById("desconto").value) || 0;
        const total = Math.max(0, subtotal - desconto);

        document.getElementById("subtotal").textContent = `R$ ${subtotal.toFixed(2).replace(".", ",")}`;
        document.getElementById("descontoDisplay").textContent = `R$ ${desconto.toFixed(2).replace(".", ",")}`;
        document.getElementById("total").textContent = `R$ ${total.toFixed(2).replace(".", ",")}`;
    }

    // Event listener para desconto
    document.getElementById("desconto").addEventListener("input", calcularTotal);
    
    // Máscara para telefone
    document.getElementById("cliente_telefone").addEventListener("input", function(e) {
        let value = e.target.value.replace(/\D/g, "");
        if (value.length >= 11) {
            value = value.replace(/(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
        } else if (value.length >= 7) {
            value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, "($1) $2-$3");
        } else if (value.length >= 3) {
            value = value.replace(/(\d{2})(\d{0,5})/, "($1) $2");
        }
        e.target.value = value;
    });
});
</script>
';

include 'layout.php';
?>