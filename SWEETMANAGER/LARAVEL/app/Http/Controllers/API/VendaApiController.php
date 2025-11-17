<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Venda;
use App\Models\Produto;
use Illuminate\Http\Request;

class VendaApiController extends Controller
{
    public function index()
    {
        return response()->json(
            Venda::with(['instituicao', 'produto'])->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'instituicao_id' => 'required|exists:instituicoes,id',
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|numeric|min:1',
        ]);

        $produto = Produto::findOrFail($validated['produto_id']);

        $validated['valor_total'] = $produto->preco * $validated['quantidade'];

        $venda = Venda::create($validated);

        return response()->json($venda, 201);
    }

    public function show($id)
    {
        $venda = Venda::with(['instituicao', 'produto'])->findOrFail($id);
        return response()->json($venda);
    }

    public function update(Request $request, $id)
    {
        $venda = Venda::findOrFail($id);

        $validated = $request->validate([
            'instituicao_id' => 'required|exists:instituicoes,id',
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|numeric|min:1',
        ]);

        $produto = Produto::findOrFail($validated['produto_id']);

        $validated['valor_total'] = $produto->preco * $validated['quantidade'];

        $venda->update($validated);

        return response()->json($venda);
    }

    public function destroy($id)
    {
        Venda::destroy($id);
        return response()->json(['message' => 'Venda deletada com sucesso']);
    }
}
