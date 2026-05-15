<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('encomendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('cliente');
            $table->text('descricao');
            $table->integer('quantidade');
            $table->decimal('valor', 10, 2);
            $table->date('data_entrega');
            $table->string('link_pagamento')->nullable();
            $table->boolean('repassado_cliente')->default(false);
            $table->boolean('pago')->default(false);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('encomendas');
    }
};
