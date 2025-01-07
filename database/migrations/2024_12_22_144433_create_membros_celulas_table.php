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
        Schema::create('membros_celulas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('idCelula');
            $table->integer('idPessoa');
            $table->boolean('status');
            $table->integer('idCliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membros_celulas');
    }
};
