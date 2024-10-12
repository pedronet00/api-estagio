<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\CategoriaRecurso;

class Saidas extends Model
{

    use HasFactory;

    protected $fillable = [
        'descricao', 'valor', 'categoria', 'data', 'idCliente',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idCliente');
    }


}
