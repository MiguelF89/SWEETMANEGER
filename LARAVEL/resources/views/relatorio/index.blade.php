<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Relatório — SweetManager</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, sans-serif; background: #f5f5f5; color: #1a1a1a; padding: 2rem 1rem; }
        .container { max-width: 1000px; margin: 0 auto; }

        h1 { font-size: 1.5rem; font-weight: 700; }
        p.sub { color: #666; font-size: .9rem; margin-bottom: 1.5rem; }

        .top-bar { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
        .top-actions { display: flex; gap: .5rem; flex-wrap: wrap; align-items: center; }

        /* Ano selector */
        .ano-select {
            padding: .4rem .8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: .9rem;
            font-weight: 600;
            color: #374151;
            background: #fff;
            cursor: pointer;
        }

        /* KPI cards */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .kpi {
            background: #fff;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
            border-left: 4px solid #e0e0e0;
        }
        .kpi.vendas   { border-color: #4f46e5; }
        .kpi.custos   { border-color: #dc2626; }
        .kpi.pendente { border-color: #d97706; }
        .kpi.lucro    { border-color: #16a34a; }

        .kpi .label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; margin-bottom: .4rem; }
        .kpi .val   { font-size: 1.4rem; font-weight: 800; }
        .kpi.vendas .val   { color: #4f46e5; }
        .kpi.custos .val   { color: #dc2626; }
        .kpi.pendente .val { color: #d97706; }
        .kpi.lucro .val    { color: #16a34a; }

        /* Charts */
        .charts { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
        @media (max-width: 600px) { .charts { grid-template-columns: 1fr; } }

        .chart-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
        }
        .chart-card.full { grid-column: 1 / -1; }
        .chart-card h2 { font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #374151; }

        /* Próximos vencimentos */
        .venc-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
            margin-bottom: 2rem;
        }
        .venc-card h2 { font-size: 1rem; font-weight: 700; margin-bottom: 1rem; }
        .venc-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .65rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .venc-item:last-child { border-bottom: none; }
        .venc-desc { font-weight: 600; font-size: .9rem; }
        .venc-date { font-size: .8rem; color: #6b7280; }
        .venc-val  { font-weight: 700; font-size: .95rem; }
        .badge { display: inline-block; padding: .15rem .5rem; border-radius: 20px; font-size: .7rem; font-weight: 700; }
        .badge-vencido  { background: #fee2e2; color: #991b1b; }
        .badge-pendente { background: #fef3c7; color: #92400e; }

        .empty { color: #9ca3af; font-size: .9rem; padding: 1rem 0; text-align: center; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: .3rem; padding: .45rem 1rem; border: none; border-radius: 8px; font-size: .85rem; font-weight: 600; cursor: pointer; text-decoration: none; }
        .btn-secondary { background: #e5e7eb; color: #374151; }
        .btn-primary   { background: #4f46e5; color: #fff; }
    </style>
</head>
<body>
<div class="container">
    <div class="top-bar">
        <div>
            <h1>📊 Relatório Financeiro</h1>
            <p class="sub">Visão geral de vendas, custos e boletos — {{ $ano }}</p>
        </div>
        <div class="top-actions">
            <form method="GET" action="{{ route('relatorio.index') }}" style="display:inline">
                <select name="ano" class="ano-select" onchange="this.form.submit()">
                    @foreach($anos as $a)
                        <option value="{{ $a }}" {{ $a == $ano ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('boleto.index') }}" class="btn btn-secondary">📋 Boletos</a>
            <a href="{{ route('boleto.reader') }}" class="btn btn-primary">+ Ler boleto</a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Painel</a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="kpi-grid">
        <div class="kpi vendas">
            <div class="label">Vendas {{ $ano }}</div>
            <div class="val">R$ {{ number_format($totais['vendas_ano'], 2, ',', '.') }}</div>
        </div>
        <div class="kpi custos">
            <div class="label">Custos pagos {{ $ano }}</div>
            <div class="val">R$ {{ number_format($totais['custos_ano'], 2, ',', '.') }}</div>
        </div>
        <div class="kpi pendente">
            <div class="label">Boletos pendentes</div>
            <div class="val">R$ {{ number_format($totais['boletos_pendentes'], 2, ',', '.') }}</div>
        </div>
        <div class="kpi lucro">
            <div class="label">Lucro estimado {{ $ano }}</div>
            <div class="val">R$ {{ number_format($totais['lucro_estimado'], 2, ',', '.') }}</div>
        </div>
    </div>

    <!-- Próximos vencimentos -->
    <div class="venc-card">
        <h2>⏰ Próximos vencimentos</h2>
        @if($proximosVencimentos->isEmpty())
            <div class="empty">Nenhum boleto pendente com vencimento cadastrado.</div>
        @else
            @foreach($proximosVencimentos as $b)
            <div class="venc-item">
                <div>
                    <div class="venc-desc">{{ $b->descricao ?: 'Sem descrição' }}</div>
                    <div class="venc-date">
                        Vence em {{ $b->vencimento->format('d/m/Y') }}
                        @if($b->vencimento->isPast())
                            <span class="badge badge-vencido">Vencido</span>
                        @else
                            <span class="badge badge-pendente">em {{ $b->vencimento->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
                <div class="venc-val">{{ $b->valor ? 'R$ ' . number_format($b->valor, 2, ',', '.') : '—' }}</div>
            </div>
            @endforeach
        @endif
    </div>

    <!-- Gráficos -->
    <div class="charts">
        <div class="chart-card full">
            <h2>Vendas vs Custos por mês ({{ $ano }})</h2>
            <canvas id="chartVendasCustos" height="100"></canvas>
        </div>
        <div class="chart-card">
            <h2>Distribuição do ano</h2>
            <canvas id="chartPizza" height="200"></canvas>
        </div>
        <div class="chart-card">
            <h2>Custos mensais</h2>
            <canvas id="chartCustos" height="200"></canvas>
        </div>
    </div>
</div>

<script>
const MESES = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
const dados  = @json($meses->values());

const vendas    = dados.map(d => d.vendas);
const custos    = dados.map(d => d.custos);
const pendentes = dados.map(d => d.pendentes);

// ── Vendas vs Custos ──────────────────────────────────────────
new Chart(document.getElementById('chartVendasCustos'), {
    type: 'bar',
    data: {
        labels: MESES,
        datasets: [
            {
                label: 'Vendas',
                data: vendas,
                backgroundColor: 'rgba(79,70,229,.75)',
                borderRadius: 6,
            },
            {
                label: 'Custos pagos',
                data: custos,
                backgroundColor: 'rgba(220,38,38,.65)',
                borderRadius: 6,
            },
            {
                label: 'Boletos pendentes',
                data: pendentes,
                backgroundColor: 'rgba(217,119,6,.55)',
                borderRadius: 6,
            },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: {
                ticks: {
                    callback: v => 'R$ ' + v.toLocaleString('pt-BR')
                }
            }
        }
    }
});

// ── Pizza ─────────────────────────────────────────────────────
const totalVendas  = vendas.reduce((a,b)  => a+b, 0);
const totalCustos  = custos.reduce((a,b)  => a+b, 0);
const totalPend    = pendentes.reduce((a,b)=> a+b, 0);

new Chart(document.getElementById('chartPizza'), {
    type: 'doughnut',
    data: {
        labels: ['Vendas', 'Custos pagos', 'Pendentes'],
        datasets: [{
            data: [totalVendas, totalCustos, totalPend],
            backgroundColor: [
                'rgba(79,70,229,.8)',
                'rgba(220,38,38,.8)',
                'rgba(217,119,6,.8)',
            ],
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: {
                callbacks: {
                    label: ctx => ' R$ ' + ctx.parsed.toLocaleString('pt-BR', { minimumFractionDigits: 2 })
                }
            }
        }
    }
});

// ── Custos mensal (linha) ─────────────────────────────────────
new Chart(document.getElementById('chartCustos'), {
    type: 'line',
    data: {
        labels: MESES,
        datasets: [{
            label: 'Custos (R$)',
            data: custos,
            borderColor: 'rgba(220,38,38,.9)',
            backgroundColor: 'rgba(220,38,38,.1)',
            fill: true,
            tension: .35,
            pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                ticks: { callback: v => 'R$' + v.toLocaleString('pt-BR') }
            }
        }
    }
});
</script>
</body>
</html>
