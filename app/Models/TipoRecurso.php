<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoRecurso extends Model
{
    use HasFactory;

    protected $table = 'tipo_recursos';

    protected $fillable = [
        'tipoRecurso',
        'idCliente'
    ];
}
