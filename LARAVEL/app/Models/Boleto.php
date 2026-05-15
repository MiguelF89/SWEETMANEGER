<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Boleto extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'barcode',
        'linha_digitavel',
        'valor',
        'vencimento',
        'banco',
        'descricao',
        'status',
        'data_pagamento',
    ];

    protected $casts = [
        'vencimento'     => 'date',
        'data_pagamento' => 'date',
        'valor'          => 'decimal:2',
    ];

    // Filtra automaticamente pelo usuário logado
    protected static function booted(): void
    {
        static::addGlobalScope('user', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('boletos.user_id', Auth::id());
            }
        });

        static::creating(function (Boleto $boleto) {
            if (Auth::check() && empty($boleto->user_id)) {
                $boleto->user_id = Auth::id();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Atualiza status para "vencido" automaticamente ao acessar
    public function getStatusAttribute($value): string
    {
        if ($value === 'pendente' && $this->vencimento && $this->vencimento->isPast()) {
            return 'vencido';
        }

        return $value;
    }

    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('status', 'pendente');
    }

    public function scopePagos(Builder $query): Builder
    {
        return $query->where('status', 'pago');
    }
}
