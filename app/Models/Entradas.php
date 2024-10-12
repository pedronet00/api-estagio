<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Clientes;
use App\Models\CategoriaRecurso;

class Entradas extends Model
{

    use HasFactory;

    protected $fillable = [
        'descricao', 'valor', 'categoria', 'data', 'idCliente',
    ];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'idCliente');
    }

    
}
