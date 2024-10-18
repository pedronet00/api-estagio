<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Recursos;
use App\Models\Departamentos;
use App\Models\Financas;
use App\Models\Eventos;
use App\Models\Entradas;
use App\Models\Saidas;

class DashboardController extends Controller
{
    public function index(Request $request)
    {   

        // Onboarding e Quantidade de Membros
        $userCount = User::where('idCliente', $request->idCliente)->count();
        $departamentoCount = Departamentos::where('idCliente', $request->idCliente)->count();
        $recursosCount = Recursos::where('idCliente', $request->idCliente)->count();


        // Saldo atual
        $saldoMensal = Financas::calcularSaldoMensal($request->idCliente);


        // Próximos eventos
        $hoje = now();
        $proximosEventos =  Eventos::where('idCliente', $request->idCliente)
            ->where('dataEvento', '>=', $hoje)
            ->with(['local'])
            ->orderBy('dataEvento')
            ->take(3)
            ->get();



        // Balanço fiscal
        $anoAtual = date('Y');

        // Inicializar arrays para armazenar entradas, saídas e saldos por mês
        $entradasPorMes = [];
        $saidasPorMes = [];
        $saldosPorMes = [];
        $nomeMeses = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];
    
        // Loop pelos meses de 1 a 12
        for ($mes = 1; $mes <= 12; $mes++) {
            // Somar as entradas e saídas do mês atual
            $entradas = Entradas::where('idCliente', $request->idCliente)
                ->whereYear('data', $anoAtual)
                ->whereMonth('data', $mes)
                ->sum('valor'); // Altere 'valor' para o campo que contém o valor da entrada
            $saidas = Saidas::where('idCliente', $request->idCliente)
                ->whereYear('data', $anoAtual)
                ->whereMonth('data', $mes)
                ->sum('valor'); // Altere 'valor' para o campo que contém o valor da saída
            // Calcular o saldo do mês (entradas - saídas)
            $saldo = $entradas - $saidas;
            // Armazenar os valores nos arrays
            $entradasPorMes[$mes] = $entradas;
            $saidasPorMes[$mes] = $saidas;
            $saldosPorMes[$mes] = $saldo; // Adiciona o saldo ao array de saldos
        }


        return response()->json([
            'onboarding' => [
                'userCount' => $userCount,
                'departamentoCount' => $departamentoCount,
                'recursosCount' => $recursosCount,
            ],
            'balancoFiscal' => [

                'entradas' => $entradasPorMes,
                'saidas' => $saidasPorMes,
                'meses' => $nomeMeses,
                'saldos' => $saldosPorMes, // Retorna o saldo por mês
            ],
            'eventos' => [
                'proximosEventos' => $proximosEventos
            ],
            'userCount' => [
                'qtdeUsuarios' => $userCount,
            ],
            'saldoAtual' => [
                'saldoAtual' => $saldoMensal
            ]
        ]);
    }
}
