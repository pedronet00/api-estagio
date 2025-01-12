<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Clientes extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'razaoSocialCliente',
        'email',
        'password',
        'idPlano',
        'session_token',
        'statusPagamento',
        'stripe_customer_id',
        'cnpj'
    ];
}
