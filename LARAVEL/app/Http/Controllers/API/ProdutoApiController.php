<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoApiController extends Controller
{
    public function index()
    {
        return response()->json(Produto::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string',
            'preco' => 'required|numeric',
        ]);

        $produto = Produto::create($validated);

        return response()->json($produto, 201);
    }

    public function show($id)
    {
        return response()->json(Produto::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $produto = Produto::findOrFail($id);
        $produto->update($request->all());

        return response()->json($produto);
    }

    public function destroy($id)
    {
        Produto::destroy($id);

        return response()->json(['message' => 'Produto deletado com sucesso']);
    }
}
