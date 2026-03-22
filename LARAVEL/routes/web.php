<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstituicaoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\VendaController;



Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('instituicoes', InstituicaoController::class)->parameters([
    'instituicoes' => 'instituicao'
    ]);
    Route::resource('produtos', ProdutoController::class);
    Route::resource('vendas', VendaController::class);
});
