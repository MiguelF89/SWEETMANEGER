<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Boletos — SweetManager</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, sans-serif; background: #f5f5f5; color: #1a1a1a; padding: 2rem 1rem; }
        .container { max-width: 900px; margin: 0 auto; }

        h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: .25rem; }
        p.sub { color: #666; font-size: .9rem; margin-bottom: 1.5rem; }

        .top-bar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
        .top-actions { display: flex; gap: .5rem; flex-wrap: wrap; }

        /* Resumo cards */
        .summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .summary-card { background: #fff; border-radius: 10px; padding: 1rem 1.25rem; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .summary-card .label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; margin-bottom: .25rem; }
        .summary-card .value { font-size: 1.3rem; font-weight: 700; }
        .summary-card.pendente .value { color: #d97706; }
        .summary-card.pago .value    { color: #16a34a; }
        .summary-card.total .value   { color: #4f46e5; }

        /* Filters */
        .filters { display: flex; gap: .5rem; margin-bottom: 1rem; flex-wrap: wrap; }
        .filter-btn {
            padding: .4rem .9rem;
            border: 2px solid #e0e0e0;
            border-radius: 20px;
            background: #fafafa;
            cursor: pointer;
            font-size: .85rem;
            font-weight: 600;
            color: #555;
            text-decoration: none;
            transition: all .15s;
        }
        .filter-btn:hover { border-color: #888; }
        .filter-btn.active { border-color: #4f46e5; background: #eef2ff; color: #4f46e5; }

        /* Table */
        .table-wrap { background: #fff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.08); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f9fafb; text-align: left; font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; padding: .75rem 1rem; border-bottom: 1px solid #e5e7eb; }
        td { padding: .85rem 1rem; border-bottom: 1px solid #f3f4f6; font-size: .9rem; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }

        /* Status badge */
        .badge { display: inline-block; padding: .2rem .6rem; border-radius: 20px; font-size: .75rem; font-weight: 700; }
        .badge-pendente { background: #fef3c7; color: #92400e; }
        .badge-pago     { background: #d1fae5; color: #065f46; }
        .badge-vencido  { background: #fee2e2; color: #991b1b; }

        /* Action buttons */
        .btn { display: inline-flex; align-items: center; gap: .3rem; padding: .35rem .8rem; border: none; border-radius: 6px; font-size: .8rem; font-weight: 600; cursor: pointer; transition: opacity .15s; text-decoration: none; }
        .btn:disabled { opacity: .4; cursor: not-allowed; }
        .btn-primary  { background: #4f46e5; color: #fff; }
        .btn-success  { background: #16a34a; color: #fff; }
        .btn-danger   { background: #dc2626; color: #fff; }
        .btn-secondary{ background: #e5e7eb; color: #374151; }
        .btn-sm { padding: .25rem .6rem; font-size: .75rem; }
        .action-group { display: flex; gap: .4rem; flex-wrap: wrap; }

        /* Empty state */
        .empty { text-align: center; padding: 3rem; color: #9ca3af; }
        .empty svg { margin-bottom: 1rem; }

        /* Pagination */
        .pagination { display: flex; justify-content: center; gap: .35rem; margin-top: 1.5rem; flex-wrap: wrap; }
        .pagination a, .pagination span {
            padding: .4rem .75rem; border-radius: 6px; font-size: .85rem; font-weight: 600;
            border: 1px solid #e0e0e0; background: #fff; color: #374151; text-decoration: none;
        }
        .pagination .active span { background: #4f46e5; color: #fff; border-color: #4f46e5; }
        .pagination a:hover { background: #f3f4f6; }

        /* Toast */
        #toast {
            position: fixed; bottom: 1.5rem; right: 1.5rem;
            background: #1a1a1a; color: #fff;
            padding: .75rem 1.25rem; border-radius: 8px;
            font-size: .9rem; font-weight: 500;
            opacity: 0; transition: opacity .3s;
            pointer-events: none; z-index: 999;
        }
        #toast.show { opacity: 1; }
    </style>
</head>
<body>
<div class="container">
    <div class="top-bar">
        <div>
            <h1>📋 Boletos</h1>
            <p class="sub">Gerencie seus boletos a pagar e pagos.</p>
        </div>
        <div class="top-actions">
            <a href="{{ route('boleto.reader') }}" class="btn btn-primary">+ Ler novo boleto</a>
            <a href="{{ route('relatorio.index') }}" class="btn btn-secondary">📊 Relatório</a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Painel</a>
        </div>
    </div>

    <!-- Resumo -->
    <div class="summary">
        <div class="summary-card pendente">
            <div class="label">A Pagar</div>
            <div class="value">R$ {{ number_format($totais['pendente'], 2, ',', '.') }}</div>
        </div>
        <div class="summary-card pago">
            <div class="label">Pago</div>
            <div class="value">R$ {{ number_format($totais['pago'], 2, ',', '.') }}</div>
        </div>
        <div class="summary-card total">
            <div class="label">Total geral</div>
            <div class="value">R$ {{ number_format($totais['total'], 2, ',', '.') }}</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters">
        <a href="{{ route('boleto.index') }}" class="filter-btn {{ $status === 'todos' ? 'active' : '' }}">Todos</a>
        <a href="{{ route('boleto.index', ['status' => 'pendente']) }}" class="filter-btn {{ $status === 'pendente' ? 'active' : '' }}">⏳ Pendentes</a>
        <a href="{{ route('boleto.index', ['status' => 'pago']) }}" class="filter-btn {{ $status === 'pago' ? 'active' : '' }}">✅ Pagos</a>
    </div>

    <!-- Tabela -->
    <div class="table-wrap">
        @if($boletos->isEmpty())
            <div class="empty">
                <svg width="48" height="48" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6M4 6h16M4 18h16"/>
                </svg>
                <p>Nenhum boleto encontrado.</p>
                <a href="{{ route('boleto.reader') }}" class="btn btn-primary" style="margin-top:1rem;width:auto;">Ler primeiro boleto</a>
            </div>
        @else
        <table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($boletos as $boleto)
                <tr id="row-{{ $boleto->id }}">
                    <td>
                        <div style="font-weight:600">{{ $boleto->descricao ?: 'Sem descrição' }}</div>
                        @if($boleto->banco)
                            <div style="font-size:.75rem;color:#9ca3af">Banco {{ $boleto->banco }}</div>
                        @endif
                    </td>
                    <td style="font-weight:700">
                        {{ $boleto->valor ? 'R$ ' . number_format($boleto->valor, 2, ',', '.') : '—' }}
                    </td>
                    <td>
                        {{ $boleto->vencimento ? $boleto->vencimento->format('d/m/Y') : '—' }}
                    </td>
                    <td>
                        <span class="badge badge-{{ $boleto->status }}">
                            {{ ucfirst($boleto->status) }}
                        </span>
                        @if($boleto->status === 'pago' && $boleto->data_pagamento)
                            <div style="font-size:.72rem;color:#6b7280;margin-top:.2rem">
                                Pago em {{ $boleto->data_pagamento->format('d/m/Y') }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="action-group">
                            @if($boleto->status !== 'pago')
                                <button class="btn btn-success btn-sm" onclick="marcarPago({{ $boleto->id }})">✓ Pago</button>
                            @endif
                            <button class="btn btn-danger btn-sm" onclick="excluir({{ $boleto->id }})">🗑</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Paginação -->
    @if($boletos->hasPages())
    <div class="pagination">
        {{ $boletos->links() }}
    </div>
    @endif
</div>

<div id="toast"></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function marcarPago(id) {
    if (!confirm('Marcar este boleto como pago?')) return;
    const res = await fetch(`/boleto/${id}/pagar`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });
    const json = await res.json();
    if (json.success) {
        showToast('Boleto marcado como pago!');
        setTimeout(() => location.reload(), 800);
    }
}

async function excluir(id) {
    if (!confirm('Excluir este boleto? Esta ação não pode ser desfeita.')) return;
    const res = await fetch(`/boleto/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });
    const json = await res.json();
    if (json.success) {
        document.getElementById('row-' + id).remove();
        showToast('Boleto excluído.');
    }
}

function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}
</script>
</body>
</html>
