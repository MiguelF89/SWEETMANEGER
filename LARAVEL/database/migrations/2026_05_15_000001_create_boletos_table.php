<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boletos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Dados extraídos do boleto
            $table->string('barcode', 48)->nullable();
            $table->string('linha_digitavel', 60)->nullable();
            $table->decimal('valor', 10, 2)->nullable();
            $table->date('vencimento')->nullable();
            $table->string('banco', 10)->nullable();
            $table->string('descricao')->nullable(); // nome/obs que o usuário pode editar

            // Controle de pagamento
            $table->enum('status', ['pendente', 'pago', 'vencido'])->default('pendente');
            $table->date('data_pagamento')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boletos');
    }
};
