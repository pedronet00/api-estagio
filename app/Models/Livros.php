<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;

class Livros extends Model
{

    use HasFactory;

    protected $fillable = [
        'nomeLivro',
        'autorLivro',
        'urlLivro',
        'idCliente'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idCliente');
    }
}
