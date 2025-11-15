<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstituicaoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\AuthApiController;

Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rotas de CRUD para Instituições
    Route::apiResource('instituicoes', InstituicaoController::class);

    // Rotas de CRUD para Produtos
    Route::apiResource('produtos', ProdutoController::class);

    // Rotas de CRUD para Vendas
    Route::apiResource('vendas', VendaController::class);
    
});
