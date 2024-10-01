<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boletins extends Model
{
    use HasFactory;

    protected $fillable = [
        'dataCulto',
        'turnoCulto',
        'transmissaoCulto',
        'filmagemCulto',
        'fotoCulto',
        'apoioCulto',
        'regenciaCulto',
        'pianoCulto',
        'orgaoCulto',
        'somCulto',
        'micVolanteCulto',
        'apoioInternetCulto',
        'cultoInfantilCulto',
        'bercarioCulto',
        'recepcaoCulto',
        'aconselhamentoCulto',
        'estacionamentoCulto',
        'diaconosCulto',
    ];
}
