<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nome', 'preco'];

    /**
     * Aplica automaticamente o filtro do usuário logado em todas as queries.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('user', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('user_id', Auth::id());
            }
        });

        static::creating(function (Produto $produto) {
            if (Auth::check() && empty($produto->user_id)) {
                $produto->user_id = Auth::id();
            }
        });
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
