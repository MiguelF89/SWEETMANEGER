<?php

namespace App\Models;

use App\Helpers\BrasilHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Instituicao extends Model
{
    use HasFactory;

    protected $table = 'instituicoes';

    protected $fillable = ['user_id', 'nome', 'contato', 'cnpj'];

    // ─── Mutators: normaliza antes de salvar ──────────────────────────────────

    public function setCnpjAttribute(?string $value): void
    {
        $this->attributes['cnpj'] = BrasilHelper::normalizeCnpj($value);
    }

    public function setContatoAttribute(?string $value): void
    {
        $this->attributes['contato'] = BrasilHelper::normalizePhone($value);
    }

    // ─── Accessors: formata ao ler ────────────────────────────────────────────

    public function getCnpjFormattedAttribute(): string
    {
        return BrasilHelper::formatCnpj($this->attributes['cnpj'] ?? '');
    }

    public function getContatoFormattedAttribute(): string
    {
        return BrasilHelper::formatPhone($this->attributes['contato'] ?? '');
    }

    // ─── Scopes e relacionamentos ─────────────────────────────────────────────

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
