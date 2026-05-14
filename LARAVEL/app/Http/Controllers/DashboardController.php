<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use App\Models\Instituicao;
use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $instituicoes = Instituicao::latest()->limit(5)->get();
        $produtos = Produto::latest()->limit(5)->get();
        $vendas = Venda::latest()->limit(5)->get();

        $totalEncomendas = $user->encomendas()->count();
        $totalPago = $user->encomendas()->where('pago', true)->count();
        $totalPendente = $user->encomendas()->where('pago', false)->count();
        $entregasProximas = $user->encomendas()
            ->whereBetween('data_entrega', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->orderBy('data_entrega')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'instituicoes' => $instituicoes,
            'produtos' => $produtos,
            'vendas' => $vendas,
            'totalEncomendas' => $totalEncomendas,
            'totalPago' => $totalPago,
            'totalPendente' => $totalPendente,
            'entregasProximas' => $entregasProximas,
        ]);
    }
}
