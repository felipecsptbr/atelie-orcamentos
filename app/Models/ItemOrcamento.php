<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemOrcamento extends Model
{
    use HasFactory;

    protected $table = 'itens_orcamento';

    protected $fillable = [
        'orcamento_id',
        'servico_id',
        'quantidade',
        'preco',
        'observacoes',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'preco' => 'decimal:2',
    ];

    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class);
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->quantidade * $this->preco;
    }
}