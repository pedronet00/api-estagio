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
        Schema::create('perfis_funcoes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('idPerfil');
            $table->integer('idFuncao');
            $table->boolean('permissao');
            $table->integer('idCliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfis_funcoes');
    }
};
