@extends('layout')

@section('title', 'Novo Orçamento')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-plus-circle text-primary"></i>
        Novo Orçamento
    </h1>
</div>

<form action="{{ route('orcamentos.store') }}" method="POST" id="orcamentoForm">
    @csrf
    
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
                        <input type="text" class="form-control @error('cliente_nome') is-invalid @enderror" 
                               id="cliente_nome" name="cliente_nome" value="{{ old('cliente_nome') }}" required>
                        @error('cliente_nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cliente_telefone" class="form-label">Telefone *</label>
                        <input type="tel" class="form-control @error('cliente_telefone') is-invalid @enderror" 
                               id="cliente_telefone" name="cliente_telefone" value="{{ old('cliente_telefone') }}" required>
                        @error('cliente_telefone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="cliente_email" class="form-label">E-mail</label>
                        <input type="email" class="form-control @error('cliente_email') is-invalid @enderror" 
                               id="cliente_email" name="cliente_email" value="{{ old('cliente_email') }}">
                        @error('cliente_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
            <div class="row">
                @foreach($servicos as $servico)
                <div class="col-md-4 mb-3">
                    <div class="card h-100 servico-card" style="cursor: pointer;" 
                         data-id="{{ $servico->id }}" 
                         data-nome="{{ $servico->nome }}"
                         data-preco="{{ $servico->preco }}">
                        <div class="card-body text-center">
                            <h6 class="card-title">{{ $servico->nome }}</h6>
                            <p class="card-text small">{{ $servico->descricao }}</p>
                            <p class="text-success fw-bold">R$ {{ number_format($servico->preco, 2, ',', '.') }}</p>
                            <button type="button" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-plus"></i> Adicionar
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
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
                        <input type="date" class="form-control @error('data_vencimento') is-invalid @enderror" 
                               id="data_vencimento" name="data_vencimento" 
                               value="{{ old('data_vencimento', date('Y-m-d', strtotime('+30 days'))) }}" required>
                        @error('data_vencimento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="desconto" class="form-label">Desconto (R$)</label>
                        <input type="number" class="form-control" id="desconto" name="desconto" 
                               step="0.01" min="0" value="{{ old('desconto', 0) }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" 
                                  rows="4">{{ old('observacoes') }}</textarea>
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
                        <span id="subtotal">R$ 0,00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Desconto:</span>
                        <span id="descontoDisplay">R$ 0,00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong id="total" class="text-success">R$ 0,00</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botões de Ação -->
    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
        <button type="submit" class="btn btn-primary btn-custom" id="salvarBtn" disabled>
            <i class="bi bi-check-circle"></i> Salvar Orçamento
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itensOrcamento = [];
    let contador = 0;

    // Adicionar serviço
    document.querySelectorAll('.servico-card').forEach(card => {
        card.addEventListener('click', function() {
            const id = this.dataset.id;
            const nome = this.dataset.nome;
            const preco = parseFloat(this.dataset.preco);

            adicionarItem(id, nome, preco);
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
        renderizarItens();
        calcularTotal();
    }

    function renderizarItens() {
        const container = document.getElementById('itensContainer');
        const card = document.getElementById('itensCard');

        if (itensOrcamento.length > 0) {
            card.style.display = 'block';
            document.getElementById('salvarBtn').disabled = false;
        } else {
            card.style.display = 'none';
            document.getElementById('salvarBtn').disabled = true;
        }

        container.innerHTML = '';

        itensOrcamento.forEach((item, index) => {
            const itemHtml = `
                <div class="card mb-3" id="item-${item.id}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <h6>${item.nome}</h6>
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
                                       value="R$ ${(item.quantidade * item.preco).toFixed(2).replace('.', ',')}" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Observações</label>
                                <textarea class="form-control" name="servicos[${index}][observacoes]" rows="2"></textarea>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-sm remover-item" 
                                        data-id="${item.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
        });

        // Adicionar event listeners
        adicionarEventListeners();
    }

    function adicionarEventListeners() {
        // Remover item
        document.querySelectorAll('.remover-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = parseInt(this.dataset.id);
                itensOrcamento = itensOrcamento.filter(item => item.id !== id);
                renderizarItens();
                calcularTotal();
            });
        });

        // Atualizar quantidade
        document.querySelectorAll('.quantidade').forEach(input => {
            input.addEventListener('change', function() {
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
        document.querySelectorAll('.preco').forEach(input => {
            input.addEventListener('change', function() {
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

        const desconto = parseFloat(document.getElementById('desconto').value) || 0;
        const total = Math.max(0, subtotal - desconto);

        document.getElementById('subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
        document.getElementById('descontoDisplay').textContent = `R$ ${desconto.toFixed(2).replace('.', ',')}`;
        document.getElementById('total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
    }

    // Event listener para desconto
    document.getElementById('desconto').addEventListener('input', calcularTotal);
});
</script>
@endsection