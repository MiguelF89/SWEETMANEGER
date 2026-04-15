<?php

namespace App\Http\Controllers\API;

use App\Helpers\BrasilHelper;
use App\Http\Controllers\Controller;
use App\Models\Instituicao;
use Illuminate\Http\Request;

class InstituicaoApiController extends Controller
{
    public function index()
    {
        // Retorna dados formatados para exibição via API
        $instituicoes = Instituicao::all()->map(function (Instituicao $inst) {
            return [
                'id'               => $inst->id,
                'nome'             => $inst->nome,
                'cnpj'             => $inst->cnpj,               // dígitos puros (storage)
                'cnpj_formatted'   => $inst->cnpj_formatted,     // 00.000.000/0000-00
                'contato'          => $inst->contato,             // dígitos puros (storage)
                'contato_formatted'=> $inst->contato_formatted,   // (XX) XXXXX-XXXX
                'user_id'          => $inst->user_id,
            ];
        });

        return response()->json($instituicoes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'    => 'required|string',
            'contato' => ['required', 'string', function ($attr, $value, $fail) {
                if (!BrasilHelper::validatePhone($value)) {
                    $fail('O contato deve ser um telefone brasileiro válido (10 ou 11 dígitos).');
                }
            }],
            'cnpj'    => ['required', 'string', function ($attr, $value, $fail) {
                if (!BrasilHelper::validateCnpj($value)) {
                    $fail('O CNPJ informado é inválido.');
                }
            }],
        ]);

        $instituicao = Instituicao::create($validated);

        return response()->json($instituicao, 201);
    }

    public function show($id)
    {
        $inst = Instituicao::findOrFail($id);

        return response()->json([
            'id'                => $inst->id,
            'nome'              => $inst->nome,
            'cnpj'              => $inst->cnpj,
            'cnpj_formatted'    => $inst->cnpj_formatted,
            'contato'           => $inst->contato,
            'contato_formatted' => $inst->contato_formatted,
            'user_id'           => $inst->user_id,
        ]);
    }

    public function update(Request $request, $id)
    {
        $instituicao = Instituicao::findOrFail($id);

        $validated = $request->validate([
            'nome'    => 'sometimes|required|string',
            'contato' => ['sometimes', 'required', 'string', function ($attr, $value, $fail) {
                if (!BrasilHelper::validatePhone($value)) {
                    $fail('O contato deve ser um telefone brasileiro válido (10 ou 11 dígitos).');
                }
            }],
            'cnpj'    => ['sometimes', 'required', 'string', function ($attr, $value, $fail) {
                if (!BrasilHelper::validateCnpj($value)) {
                    $fail('O CNPJ informado é inválido.');
                }
            }],
        ]);

        $instituicao->update($validated);

        return response()->json($instituicao);
    }

    public function destroy($id)
    {
        Instituicao::destroy($id);

        return response()->json(['message' => 'Deletado com sucesso']);
    }
}
