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
        Schema::table('locais', function (Blueprint $table) {
            $table->integer('idCliente');
        });

        Schema::table('categoria_recursos', function (Blueprint $table) {
            $table->integer('idCliente');
        });

        Schema::table('tipo_recursos', function (Blueprint $table) {
            $table->integer('idCliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
