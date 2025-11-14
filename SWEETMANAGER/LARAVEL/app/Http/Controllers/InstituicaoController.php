<?php

namespace App\Http\Controllers;

use App\Models\Instituicao;
use Illuminate\Http\Request;

class InstituicaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instituicoes = Instituicao::paginate(10);
        return view('instituicoes.index', compact('instituicoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('instituicoes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string',
            'contato' => 'required|string',
            'cnpj' => 'required|string',
        ]);

        Instituicao::create($validated);

        return redirect()->route('instituicoes.index')->with('success', 'Instituição criada com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instituicao $instituicao)
    {
        return view('instituicoes.edit', compact('instituicao'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Instituicao $instituicao)
    {
        $validated = $request->validate([
            'nome' => 'required|string',
            'contato' => 'required|string',
            'cnpj' => 'required|string',
        ]);

        $instituicao->update($validated);

        return redirect()->route('instituicoes.index')->with('success', 'Instituição atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instituicao $instituicao)
    {
        $instituicao->delete();

        return redirect()->route('instituicoes.index')->with('success', 'Instituição deletada com sucesso!');
    }
}
