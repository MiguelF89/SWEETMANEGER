<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthApiController;
use App\Http\Controllers\API\InstituicaoApiController;
use App\Http\Controllers\API\ProdutoApiController;
use App\Http\Controllers\API\VendaApiController;


Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    Route::apiResource('instituicoes', InstituicaoApiController::class);
    Route::apiResource('produtos', ProdutoApiController::class);
    Route::apiResource('vendas', VendaApiController::class);
});
