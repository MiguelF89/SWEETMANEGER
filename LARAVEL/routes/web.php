<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EncomendaController;
use App\Http\Controllers\InstituicaoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\BoletoController;
use App\Http\Controllers\RelatorioController;

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
    Route::resource('encomendas', EncomendaController::class);

    // ── Boleto Reader + Lista ──────────────────────────────────
    Route::get('/boleto/reader', [BoletoController::class, 'reader'])->name('boleto.reader');
    Route::post('/boleto/read',  [BoletoController::class, 'read'])->name('boleto.read');
    Route::get('/boleto',        [BoletoController::class, 'index'])->name('boleto.index');
    Route::post('/boleto/{boleto}/pagar', [BoletoController::class, 'pagar'])->name('boleto.pagar');
    Route::patch('/boleto/{boleto}',      [BoletoController::class, 'update'])->name('boleto.update');
    Route::delete('/boleto/{boleto}',     [BoletoController::class, 'destroy'])->name('boleto.destroy');

    // ── Relatório ─────────────────────────────────────────────
    Route::get('/relatorio', [RelatorioController::class, 'index'])->name('relatorio.index');
});
