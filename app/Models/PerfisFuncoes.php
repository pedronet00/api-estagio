<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Perfis;
use App\Models\Funcoes;

class PerfisFuncoes extends Model
{

    use HasFactory;

    protected $fillable = [
        'idPerfil',
        'idFuncao',
        'permissao',
        'idCliente'
    ];

    public function perfil()
    {
        return $this->belongsTo(Perfis::class, 'idPerfil');
    }

    public function funcao()
    {
        return $this->belongsTo(Funcoes::class, 'idFuncao');
    }
}
