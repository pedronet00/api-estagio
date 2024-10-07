<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Locais;

class Eventos extends Model
{
    use HasFactory;

    protected $fillable = [
        "nomeEvento",
        "descricaoEvento",
        "localEvento",
        "dataEvento",
        "prioridadeEvento",
        "orcamentoEvento"
    ];

    public function local()
    {
        return $this->belongsTo(Locais::class, 'localEvento');
    }
}
