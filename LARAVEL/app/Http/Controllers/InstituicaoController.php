<?php

namespace App\Http\Controllers;

use App\Models\Instituicao;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InstituicaoController extends Controller
{
    public function index()
    {
        // Global scope já filtra por user_id automaticamente
        $instituicoes = Instituicao::paginate(10);
        return view('instituicoes.index', compact('instituicoes'));
    }

    public function create()
    {
        return view('instituicoes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'    => 'required|string',
            'contato' => 'required|string',
            'cnpj'    => 'required|string',
        ]);

        Instituicao::create($validated);

        return redirect()->route('instituicoes.index')->with('success', 'Instituição criada com sucesso!');
    }

    public function edit(Instituicao $instituicao)
    {
        return view('instituicoes.edit', compact('instituicao'));
    }

    public function update(Request $request, Instituicao $instituicao)
    {
        $validated = $request->validate([
            'nome'    => 'required|string',
            'contato' => 'required|string',
            'cnpj'    => 'required|string',
        ]);

        $instituicao->update($validated);

        return redirect()->route('instituicoes.index')->with('success', 'Instituição atualizada com sucesso!');
    }

    public function destroy(Instituicao $instituicao)
    {
        $instituicao->delete();
        return redirect()->route('instituicoes.index')->with('success', 'Instituição deletada com sucesso!');
    }
}
