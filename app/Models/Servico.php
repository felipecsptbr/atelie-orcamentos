<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'categoria',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
    ];

    public function itensOrcamento()
    {
        return $this->hasMany(ItemOrcamento::class);
    }
}