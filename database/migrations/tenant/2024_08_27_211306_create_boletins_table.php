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
        Schema::create('boletins', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('dataCulto');
            $table->integer('turnoCulto');
            $table->string('transmissaoCulto');
            $table->string('filmagemCulto');
            $table->string('fotoCulto');
            $table->string('apoioCulto');
            $table->string('regenciaCulto');
            $table->string('pianoCulto');
            $table->string('orgaoCulto');
            $table->string('somCulto');
            $table->string('micVolanteCulto');
            $table->string('apoioInternetCulto');
            $table->string('cultoInfantilCulto');
            $table->string('bercarioCulto');
            $table->string('recepcaoCulto');
            $table->string('aconselhamentoCulto');
            $table->string('estacionamentoCulto');
            $table->string('diaconosCulto');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletins');
    }
};
