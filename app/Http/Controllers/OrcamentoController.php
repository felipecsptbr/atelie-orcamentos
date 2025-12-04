<?php

namespace App\Http\Controllers;

use App\Models\Orcamento;
use App\Models\Cliente;
use App\Models\Servico;
use App\Models\ItemOrcamento;
use Illuminate\Http\Request;
use PDF;

class OrcamentoController extends Controller
{
    public function index()
    {
        $orcamentos = Orcamento::with(['cliente', 'itens.servico'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orcamentos.index', compact('orcamentos'));
    }

    public function create()
    {
        $servicos = Servico::all();
        return view('orcamentos.create', compact('servicos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_nome' => 'required|string|max:255',
            'cliente_telefone' => 'required|string|max:255',
            'cliente_email' => 'nullable|email|max:255',
            'data_vencimento' => 'required|date|after:today',
            'servicos' => 'required|array|min:1',
            'servicos.*.id' => 'required|exists:servicos,id',
            'servicos.*.quantidade' => 'required|integer|min:1',
            'servicos.*.preco' => 'required|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string',
        ]);

        // Criar ou encontrar cliente
        $cliente = Cliente::firstOrCreate([
            'telefone' => $request->cliente_telefone
        ], [
            'nome' => $request->cliente_nome,
            'email' => $request->cliente_email,
        ]);

        // Criar orçamento
        $orcamento = Orcamento::create([
            'numero' => $this->gerarNumeroOrcamento(),
            'cliente_id' => $cliente->id,
            'data_vencimento' => $request->data_vencimento,
            'status' => 'pendente',
            'observacoes' => $request->observacoes,
            'desconto' => $request->desconto ?? 0,
            'total' => 0, // Será calculado depois
        ]);

        // Adicionar itens
        $total = 0;
        foreach ($request->servicos as $servicoData) {
            $item = ItemOrcamento::create([
                'orcamento_id' => $orcamento->id,
                'servico_id' => $servicoData['id'],
                'quantidade' => $servicoData['quantidade'],
                'preco' => $servicoData['preco'],
                'observacoes' => $servicoData['observacoes'] ?? null,
            ]);
            
            $total += $item->quantidade * $item->preco;
        }

        // Atualizar total do orçamento
        $orcamento->update(['total' => $total - ($request->desconto ?? 0)]);

        return redirect()->route('orcamentos.show', $orcamento)
            ->with('success', 'Orçamento criado com sucesso!');
    }

    public function show(Orcamento $orcamento)
    {
        $orcamento->load(['cliente', 'itens.servico']);
        return view('orcamentos.show', compact('orcamento'));
    }

    public function pdf(Orcamento $orcamento)
    {
        $orcamento->load(['cliente', 'itens.servico']);
        
        $pdf = PDF::loadView('orcamentos.pdf', compact('orcamento'));
        
        return $pdf->download('orcamento-' . $orcamento->numero . '.pdf');
    }

    private function gerarNumeroOrcamento()
    {
        $ano = date('Y');
        $mes = date('m');
        $dia = date('d');
        
        $ultimo = Orcamento::whereDate('created_at', today())
            ->count();
        
        $sequencial = str_pad($ultimo + 1, 3, '0', STR_PAD_LEFT);
        
        return "ORC-{$ano}{$mes}{$dia}-{$sequencial}";
    }
}