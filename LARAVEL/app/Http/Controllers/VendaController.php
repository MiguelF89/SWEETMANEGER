<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Instituicao;
use App\Models\Produto;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    public function index()
    {
        $vendas = Venda::with(['instituicao', 'produto'])->paginate(10);
        return view('vendas.index', compact('vendas'));
    }

    public function create()
    {
        // Mostra apenas instituições e produtos do usuário logado
        $instituicoes = Instituicao::all();
        $produtos = Produto::all();
        return view('vendas.create', compact('instituicoes', 'produtos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'instituicao_id' => 'required|exists:instituicoes,id',
            'produto_id'     => 'required|exists:produtos,id',
            'quantidade'     => 'required|numeric|min:1',
        ]);

        // Verifica que a instituição e o produto pertencem ao usuário logado
        $produto = Produto::findOrFail($validated['produto_id']);
        Instituicao::findOrFail($validated['instituicao_id']); // lança 404 se não for do user

        $validated['valor_total'] = $produto->preco * $validated['quantidade'];

        Venda::create($validated);

        return redirect()->route('vendas.index')->with('success', 'Venda criada com sucesso!');
    }

    public function edit(Venda $venda)
    {
        $instituicoes = Instituicao::all();
        $produtos = Produto::all();
        return view('vendas.edit', compact('venda', 'instituicoes', 'produtos'));
    }

    public function update(Request $request, Venda $venda)
    {
        $validated = $request->validate([
            'instituicao_id' => 'required|exists:instituicoes,id',
            'produto_id'     => 'required|exists:produtos,id',
            'quantidade'     => 'required|numeric|min:1',
        ]);

        $produto = Produto::findOrFail($validated['produto_id']);
        Instituicao::findOrFail($validated['instituicao_id']);

        $validated['valor_total'] = $produto->preco * $validated['quantidade'];

        $venda->update($validated);

        return redirect()->route('vendas.index')->with('success', 'Venda atualizada com sucesso!');
    }

    public function destroy(Venda $venda)
    {
        $venda->delete();
        return redirect()->route('vendas.index')->with('success', 'Venda deletada com sucesso!');
    }
}
