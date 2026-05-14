<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Encomenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cliente',
        'descricao',
        'quantidade',
        'valor',
        'data_entrega',
        'horario_entrega',
        'link_pagamento',
        'repassado_cliente',
        'pago',
        'observacoes',
    ];

    protected $casts = [
        'data_entrega' => 'date',
        'horario_entrega' => 'string',
        'repassado_cliente' => 'boolean',
        'pago' => 'boolean',
        'valor' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
