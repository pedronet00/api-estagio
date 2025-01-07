<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('escalas_cultos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('idCulto');
            $table->integer('idFuncaoCulto');
            $table->integer('idPessoa');
            $table->integer('idCliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalas_cultos');
    }
};
