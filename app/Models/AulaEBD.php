<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClassesEBD;
use App\Models\User;

class AulaEBD extends Model
{
    use HasFactory;

    protected $table = 'aula_ebd';

    protected $fillable = [
        'dataAula',
        'classeAula',
        'professorAula',
        'quantidadePresentes',
        'numeroAula',
        'idCliente'
    ];

    public function classe()
    {
        return $this->belongsTo(ClassesEBD::class, 'classeAula');
    }

    public function professor()
    {
        return $this->belongsTo(User::class, 'professorAula');
    }
}
