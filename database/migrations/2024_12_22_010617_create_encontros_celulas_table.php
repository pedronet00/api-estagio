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
        Schema::create('encontros_celulas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('idCelula');
            $table->integer('idPessoaEstudo');
            $table->string('temaEstudo');
            $table->integer('idLocal');
            $table->integer('qtdePresentes')->nullable();
            $table->integer('idCliente');
            $table->date('dataEncontro');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encontros_celulas');
    }
};
