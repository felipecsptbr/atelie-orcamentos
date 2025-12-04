<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'cliente_id',
        'data_vencimento',
        'status',
        'observacoes',
        'desconto',
        'total',
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'desconto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function itens()
    {
        return $this->hasMany(ItemOrcamento::class);
    }

    public function calcularTotal()
    {
        $subtotal = $this->itens->sum(function ($item) {
            return $item->quantidade * $item->preco;
        });
        
        return $subtotal - $this->desconto;
    }
}