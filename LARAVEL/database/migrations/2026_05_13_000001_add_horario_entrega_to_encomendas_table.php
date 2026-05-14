<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('encomendas', function (Blueprint $table) {
            $table->time('horario_entrega')->nullable()->after('data_entrega');
        });
    }

    public function down()
    {
        Schema::table('encomendas', function (Blueprint $table) {
            $table->dropColumn('horario_entrega');
        });
    }
};
