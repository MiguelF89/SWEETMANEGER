<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstituicaoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\API\InstituicaoApiController;

Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    Route::apiResource('instituicoes', InstituicaoApiController::class);


    Route::apiResource('produtos', ProdutoController::class);


    Route::apiResource('vendas', VendaController::class);
    
});
