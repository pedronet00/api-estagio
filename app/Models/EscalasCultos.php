<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Culto;
use App\Models\FuncoesCulto;
use App\Models\User;

class EscalasCultos extends Model
{

    use HasFactory;

    protected $fillable = [
        'idCulto',
        'idFuncaoCulto',
        'idPessoa',
        'idCliente'
    ];

    public function culto()
    {
        return $this->belongsTo(Culto::class, 'idCulto');
    }

    public function funcaoCulto()
    {
        return $this->belongsTo(FuncoesCulto::class, 'idFuncaoCulto');
    }

    public function pessoa()
    {
        return $this->belongsTo(User::class, 'idPessoa');
    }
}
