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
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nomePlano');
            $table->integer('qtdeUsuarios');
            $table->integer('qtdeDepartamentos');
            $table->integer('qtdeMissoes');
            $table->integer('qtdeCelulas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos');
    }
};
