<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Missoes extends Model
{
    use HasFactory;

    protected $fillable = [
        "nomeMissao",
        "quantidadeMembros",
        "cidadeMissao",
        "pastorTitular",
        "statusMissao",
        "idCliente"
    ];

    public function pastorTitular()
    {
        return $this->belongsTo(User::class, 'pastorTitular');
    }
}
