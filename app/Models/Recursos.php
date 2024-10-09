<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoRecurso;
use App\Models\CategoriaRecurso;

class Recursos extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomeRecurso',
        'tipoRecurso',
        'categoriaRecurso',
        'quantidadeRecurso',
        'idCliente'
    ];

    public function tipo()
    {
        return $this->belongsTo(TipoRecurso::class, 'tipoRecurso');
    }

    // Definindo a relação com TipoPost
    public function categoria()
    {
        return $this->belongsTo(CategoriaRecurso::class, 'categoriaRecurso');
    }
}
