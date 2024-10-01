<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\TipoPost;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'tituloPost',
        'subtituloPost',
        'autorPost',
        'dataPost',
        'textoPost',
        'imgPost',
        'tipoPost',
        'statusPost'
    ];

    public function autor()
    {
        return $this->belongsTo(User::class, 'autorPost');
    }

    // Definindo a relação com TipoPost
    public function tipo()
    {
        return $this->belongsTo(TipoPost::class, 'tipoPost');
    }
}
