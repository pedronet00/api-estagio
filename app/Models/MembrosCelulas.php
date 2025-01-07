<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Celulas;

class MembrosCelulas extends Model
{

    use HasFactory;

    protected $fillable = [
        'idCelula',
        'idPessoa',
        'status',
        'idCliente'
    ];

    public function pessoa()
    {
        return $this->belongsTo(User::class, 'idPessoa');
    }

    public function celula()
    {
        return $this->belongsTo(Celulas::class, 'idCelula');
    }
}
