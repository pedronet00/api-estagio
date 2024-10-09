<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NivelUsuario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nivelUsuario',
        'idCliente'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'nivelUsuario', 'id');
    }
}
