<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Instituicao;
use App\Models\Produto;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendas = Venda::with(['instituicao', 'produto'])->paginate(10);
        return view('vendas.index', compact('vendas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $instituicoes = Instituicao::all();
        $produtos = Produto::all();
        return view('vendas.create', compact('instituicoes', 'produtos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'instituicao_id' => 'required|exists:instituicoes,id',
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|numeric|min:1',
        ]);

        $produto = Produto::find($validated['produto_id']);
        $validated['valor_total'] = $produto->preco * $validated['quantidade'];

        Venda::create($validated);

        return redirect()->route('vendas.index')->with('success', 'Venda criada com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venda $venda)
    {
        $instituicoes = Instituicao::all();
        $produtos = Produto::all();
        return view('vendas.edit', compact('venda', 'instituicoes', 'produtos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venda $venda)
    {
        $validated = $request->validate([
            'instituicao_id' => 'required|exists:instituicoes,id',
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|numeric|min:1',
        ]);

        $produto = Produto::find($validated['produto_id']);
        $validated['valor_total'] = $produto->preco * $validated['quantidade'];

        $venda->update($validated);

        return redirect()->route('vendas.index')->with('success', 'Venda atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venda $venda)
    {
        $venda->delete();

        return redirect()->route('vendas.index')->with('success', 'Venda deletada com sucesso!');
    }
}
