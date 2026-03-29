<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Instituicao extends Model
{
    use HasFactory;

    protected $table = 'instituicoes';

    protected $fillable = ['user_id', 'nome', 'contato', 'cnpj'];

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

        static::creating(function (Instituicao $instituicao) {
            if (Auth::check() && empty($instituicao->user_id)) {
                $instituicao->user_id = Auth::id();
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
