<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Locais;

class Culto extends Model
{

    use HasFactory;

    protected $fillable = [
        'dataCulto',
        'turnoCulto',
        'localCulto',
        'idCliente'
    ];

    public function local()
    {
        return $this->belongsTo(Locais::class, 'localCulto');
    }
}
