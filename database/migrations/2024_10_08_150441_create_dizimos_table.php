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
        Schema::create('dizimos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('dataCulto');
            $table->integer('turnoCulto');
            $table->decimal('valorArrecadado', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dizimos');
    }
};
