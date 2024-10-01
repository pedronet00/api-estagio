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
            // Alterar o tipo da coluna imgUsuario de text para varchar com limite de 255 caracteres
            $table->string('name', 50)->change();
            $table->string('email', 50)->change();
            $table->string('imgUsuario', 100)->nullable()->change();
        });
        Schema::table('posts', function (Blueprint $table) {
            // Alterar o tipo da coluna imgUsuario de text para varchar com limite de 255 caracteres
            $table->string('tituloPost', 50)->change();
            $table->string('subtituloPost', 150)->change();
            $table->string('textoPost', 4000)->change();
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
