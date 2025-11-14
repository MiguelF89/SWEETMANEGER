<?php

namespace App\Http\Controllers;

use App\Models\Instituicao;
use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $instituicoes = Instituicao::latest()->limit(5)->get();
        $produtos = Produto::latest()->limit(5)->get();
        $vendas = Venda::latest()->limit(5)->get();

        return view('dashboard', [
            'instituicoes' => $instituicoes,
            'produtos' => $produtos,
            'vendas' => $vendas,
        ]);
    }
}
