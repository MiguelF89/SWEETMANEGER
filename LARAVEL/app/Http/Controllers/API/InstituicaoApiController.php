<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Instituicao;
use Illuminate\Http\Request;

class InstituicaoApiController extends Controller
{
    public function index()
    {
        return response()->json(Instituicao::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string',
            'contato' => 'required|string',
            'cnpj' => 'required|string',
        ]);

        $instituicao = Instituicao::create($validated);

        return response()->json($instituicao, 201);
    }

    public function show($id)
    {
        return response()->json(Instituicao::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $instituicao = Instituicao::findOrFail($id);
        $instituicao->update($request->all());

        return response()->json($instituicao);
    }

    public function destroy($id)
    {
        Instituicao::destroy($id);

        return response()->json(['message' => 'Deletado com sucesso']);
    }
}
