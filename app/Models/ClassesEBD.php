<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassesEBD extends Model
{
    use HasFactory;

    protected $table = 'classes_ebd';

    protected $fillable = [
        'nomeClasse',
        'quantidadeMembros',
        'statusClasse',
        'idCliente'
    ];
}
