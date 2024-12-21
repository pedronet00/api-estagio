<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Locais;
use App\Models\User;

class Celulas extends Model
{

    use HasFactory;

    protected $fillable = [
        "nomeCelula",
        "localizacaoCelula",
        "responsavelCelula",
        "diaReuniao",
        "idCliente",
        "imagemCelula"
    ];

    public function localizacao()
    {
        return $this->belongsTo(Locais::class, 'localizacaoCelula');
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavelCelula');
    }
}
