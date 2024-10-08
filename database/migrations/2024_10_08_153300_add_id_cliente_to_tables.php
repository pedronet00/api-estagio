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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('idCliente');
        });

        Schema::table('departamentos', function (Blueprint $table) {
            $table->integer('idCliente');
        });

        Schema::table('dizimos', function (Blueprint $table) {
            $table->integer('idCliente');
        });

        Schema::table('eventos', function (Blueprint $table) {
            $table->integer('idCliente');
        });

        Schema::table('missoes', function (Blueprint $table) {
            $table->integer('idCliente');
        });

        Schema::table('recursos', function (Blueprint $table) {
            $table->integer('idCliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
