<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\Venda;
use App\Models\Encomenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RelatorioController extends Controller
{
    public function index(Request $request)
    {
        $ano = $request->get('ano', now()->year);

        // ── Vendas por mês ─────────────────────────────────────
        $vendasPorMes = Venda::selectRaw('MONTH(created_at) as mes, SUM(valor_total) as total')
            ->whereYear('created_at', $ano)
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        // ── Boletos pagos por mês (custos) ─────────────────────
        $custosPorMes = Boleto::selectRaw('MONTH(data_pagamento) as mes, SUM(valor) as total')
            ->where('status', 'pago')
            ->whereYear('data_pagamento', $ano)
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        // ── Boletos pendentes por mês (vencimento) ─────────────
        $pendentesPorMes = Boleto::selectRaw('MONTH(vencimento) as mes, SUM(valor) as total')
            ->where('status', 'pendente')
            ->whereYear('vencimento', $ano)
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        // Preenche todos os 12 meses mesmo sem dados
        $meses = collect(range(1, 12))->mapWithKeys(fn($m) => [
            $m => [
                'vendas'     => (float) ($vendasPorMes[$m]    ?? 0),
                'custos'     => (float) ($custosPorMes[$m]    ?? 0),
                'pendentes'  => (float) ($pendentesPorMes[$m] ?? 0),
            ]
        ]);

        // ── Totais gerais ──────────────────────────────────────
        $totais = [
            'vendas_ano'       => Venda::whereYear('created_at', $ano)->sum('valor_total'),
            'custos_ano'       => Boleto::where('status', 'pago')->whereYear('data_pagamento', $ano)->sum('valor'),
            'boletos_pendentes' => Boleto::where('status', 'pendente')->sum('valor'),
            'boletos_pagos'    => Boleto::where('status', 'pago')->sum('valor'),
            'lucro_estimado'   => Venda::whereYear('created_at', $ano)->sum('valor_total')
                                - Boleto::where('status', 'pago')->whereYear('data_pagamento', $ano)->sum('valor'),
        ];

        // ── Próximos vencimentos ────────────────────────────────
        $proximosVencimentos = Boleto::where('status', 'pendente')
            ->whereNotNull('vencimento')
            ->orderBy('vencimento')
            ->limit(5)
            ->get();

        $anos = range(now()->year - 2, now()->year + 1);

        return view('relatorio.index', compact(
            'meses', 'totais', 'proximosVencimentos', 'ano', 'anos'
        ));
    }
}
