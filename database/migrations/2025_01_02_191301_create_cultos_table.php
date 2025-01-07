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
        Schema::create('cultos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('dataCulto');
            $table->integer('turnoCulto');
            $table->integer('idCliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cultos');
    }
};
