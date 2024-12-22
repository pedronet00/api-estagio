<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Locais;
use App\Models\User;
use App\Models\Celulas;

class EncontrosCelulas extends Model
{

    use HasFactory;

    protected $fillable = [
        'idCelula',
        'idPessoaEstudo',
        'temaEstudo',
        'idLocal',
        'qtdePresentes',
        'dataEncontro',
        'idCliente'
    ];

    public function celula()
    {
        return $this->belongsTo(Celulas::class, 'idCelula');
    }

    public function localizacao()
    {
        return $this->belongsTo(Locais::class, 'idLocal');
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'idPessoaEstudo');
    }
}
