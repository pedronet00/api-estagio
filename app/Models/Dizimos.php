<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dizimos extends Model
{
    use HasFactory;

    protected $fillable = [
        'dataCulto',
        'turnoCulto',
        'valorArrecadado',
        'idCliente'
    ];
}
