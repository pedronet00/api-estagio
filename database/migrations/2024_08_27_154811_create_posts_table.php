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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('tituloPost');
            $table->string('subtituloPost');
            $table->integer('autorPost');
            $table->date('dataPost');
            $table->text('textoPost');
            $table->string('imgPost');
            $table->integer('tipoPost');
            $table->boolean('statusPost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
