<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTables extends Migration
{
    /**
     * Execute a migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('tipo_recurso', 'tipo_recursos');
        Schema::rename('categoria_recurso', 'categoria_recursos');
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('tipo_recursos', 'tipo_recurso');
        Schema::rename('categoria_recursos', 'categoria_recurso');
    }
}
