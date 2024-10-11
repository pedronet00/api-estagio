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
        Schema::create('classes_ebd', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nomeClasse');
            $table->integer('quantidadeMembros');
            $table->integer('statusClasse');
            $table->integer('idCliente');
        }, 'classes_ebd');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes_ebd');
    }
};
