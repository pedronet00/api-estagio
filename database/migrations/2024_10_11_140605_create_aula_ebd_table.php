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
        Schema::create('aula_ebd', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('dataAula');
            $table->integer('classeAula');
            $table->integer('professorAula');
            $table->integer('quantidadePresentes');
            $table->integer('numeroAula');
            $table->integer('idCliente');
        }, 'aulas_ebd');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aula_ebd');
    }
};
