<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Financas extends Model
{

    use HasFactory;

    protected $fillable = ['saldoMensal'];

    // Função que calcula o saldo mensal do mês atual
    public static function calcularSaldoMensal($idCliente, $inicio, $fim)
    {
       

        // Somando todas as entradas do mês atual para o cliente
        $totalEntradas = DB::table('entradas')
            ->where('idCliente', $idCliente)
            ->whereBetween('data', [$inicio, $fim])
            ->sum('valor');

        // Somando todas as saídas do mês atual para o cliente
        $totalSaidas = DB::table('saidas')
            ->where('idCliente', $idCliente)
            ->whereBetween('data', [$inicio, $fim])
            ->sum('valor');

        // Calculando o saldo
        $saldoMensal = $totalEntradas - $totalSaidas;

        // Retornar o saldo mensal
        return $saldoMensal;
    }
}
