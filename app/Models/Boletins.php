<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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

    public function transmissao()
    {
        return $this->belongsTo(User::class, 'transmissaoCulto');
    }

    public function filmagem()
    {
        return $this->belongsTo(User::class, 'filmagemCulto');
    }

    public function foto()
    {
        return $this->belongsTo(User::class, 'fotoCulto');
    }

    public function apoio()
    {
        return $this->belongsTo(User::class, 'apoioCulto');
    }

    public function regencia()
    {
        return $this->belongsTo(User::class, 'regenciaCulto');
    }

    public function piano()
    {
        return $this->belongsTo(User::class, 'pianoCulto');
    }

    public function orgao()
    {
        return $this->belongsTo(User::class, 'orgaoCulto');
    }

    public function som()
    {
        return $this->belongsTo(User::class, 'somCulto');
    }

    public function micVolante()
    {
        return $this->belongsTo(User::class, 'micVolanteCulto');
    }

    public function apoioInternet()
    {
        return $this->belongsTo(User::class, 'apoioInternetCulto');
    }

    public function cultoInfantil()
    {
        return $this->belongsTo(User::class, 'cultoInfantilCulto');
    }

    public function bercario()
    {
        return $this->belongsTo(User::class, 'bercarioCulto');
    }

    public function recepcao()
    {
        return $this->belongsTo(User::class, 'recepcaoCulto');
    }

    public function aconselhamento()
    {
        return $this->belongsTo(User::class, 'aconselhamentoCulto');
    }

    public function estacionamento()
    {
        return $this->belongsTo(User::class, 'estacionamentoCulto');
    }

    public function diaconos()
    {
        return $this->belongsTo(User::class, 'diaconosCulto');
    }
}
