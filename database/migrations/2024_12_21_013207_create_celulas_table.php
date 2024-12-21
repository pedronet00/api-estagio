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
        Schema::create('celulas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nomeCelula');
            $table->integer('localizacaoCelula');
            $table->integer('responsavelCelula');
            $table->integer('diaReuniao');
            $table->integer('idCliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('celulas', function (Blueprint $table) {
            Schema::dropIfExists('celulas');
        });
    }
};
