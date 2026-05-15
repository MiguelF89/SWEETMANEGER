<?php

namespace App\Http\Controllers;

use App\Http\Requests\BoletoReadRequest;
use App\Models\Boleto;
use App\Services\BoletoReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BoletoController extends Controller
{
    // ── Leitor (página) ────────────────────────────────────────
    public function reader()
    {
        return view('boleto.reader');
    }

    // ── Lê o arquivo e SALVA automaticamente na lista ──────────
    public function read(BoletoReadRequest $request): JsonResponse
    {
        try {
            $service = app(BoletoReaderService::class);
            $data = $service->read($request->file('file'));

            // Salva automaticamente na tabela de boletos
            $boleto = Boleto::create([
                'barcode'        => $data['barcode']        ?? null,
                'linha_digitavel' => $data['linha_digitavel'] ?? null,
                'valor'          => $data['amount']         ?? null,
                'vencimento'     => $data['due_date']       ?? null,
                'banco'          => $data['bank']           ?? null,
                'descricao'      => $request->input('descricao', 'Boleto importado automaticamente'),
                'status'         => 'pendente',
            ]);

            return response()->json([
                'success' => true,
                'data'    => array_merge($data, ['id' => $boleto->id]),
                'message' => 'Boleto lido e adicionado à lista de pagamentos.',
            ]);

        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'error' => 'Erro interno.'], 500);
        }
    }

    // ── Lista de boletos ───────────────────────────────────────
    public function index(Request $request)
    {
        $status = $request->get('status', 'todos');

        $query = Boleto::orderBy('vencimento');

        if ($status === 'pendente') {
            $query->where('status', 'pendente');
        } elseif ($status === 'pago') {
            $query->where('status', 'pago');
        }

        $boletos = $query->paginate(15)->withQueryString();

        $totais = [
            'pendente' => Boleto::where('status', 'pendente')->sum('valor'),
            'pago'     => Boleto::where('status', 'pago')->sum('valor'),
            'total'    => Boleto::sum('valor'),
        ];

        return view('boleto.index', compact('boletos', 'totais', 'status'));
    }

    // ── Marcar como pago ───────────────────────────────────────
    public function pagar(Request $request, Boleto $boleto): JsonResponse
    {
        $boleto->update([
            'status'         => 'pago',
            'data_pagamento' => $request->input('data_pagamento', now()->toDateString()),
        ]);

        return response()->json(['success' => true, 'message' => 'Boleto marcado como pago.']);
    }

    // ── Editar descrição ───────────────────────────────────────
    public function update(Request $request, Boleto $boleto): JsonResponse
    {
        $request->validate(['descricao' => 'required|string|max:255']);

        $boleto->update(['descricao' => $request->descricao]);

        return response()->json(['success' => true]);
    }

    // ── Excluir ────────────────────────────────────────────────
    public function destroy(Boleto $boleto): JsonResponse
    {
        $boleto->delete();

        return response()->json(['success' => true]);
    }
}
