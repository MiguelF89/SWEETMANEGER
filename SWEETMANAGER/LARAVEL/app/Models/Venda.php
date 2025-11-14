<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = ['instituicao_id', 'produto_id', 'quantidade', 'valor_total'];

    public function instituicao()
    {
        return $this->belongsTo(Instituicao::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
