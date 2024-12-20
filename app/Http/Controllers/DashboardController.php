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

    // Usuários cadastrados no último mês e no mês atual
    $mesAtual = now()->month;
    $mesPassado = $mesAtual - 1;
    $anoAtual = now()->year;

    $usuariosMesAtual = User::where('idCliente', $request->idCliente)
        ->whereYear('created_at', $anoAtual)
        ->whereMonth('created_at', $mesAtual)
        ->count();

    $usuariosMesPassado = User::where('idCliente', $request->idCliente)
        ->whereYear('created_at', $anoAtual)
        ->whereMonth('created_at', $mesPassado)
        ->count();

    // Comparação percentual entre o mês atual e o mês passado
    $percentualAumento = $usuariosMesPassado > 0
        ? (($usuariosMesAtual - $usuariosMesPassado) / $usuariosMesPassado) * 100
        : ($usuariosMesAtual > 0 ? 100 : 0);

    $percentualAumento = round($percentualAumento, 2); // Arredonda para 2 casas decimais

    // Quantidade de Departamentos e Recursos
    $departamentoCount = Departamentos::where('idCliente', $request->idCliente)->count();
    $recursosCount = Recursos::where('idCliente', $request->idCliente)->count();

    // Saldo do mês atual
    $entradasMesAtual = Entradas::where('idCliente', $request->idCliente)
        ->whereYear('data', $anoAtual)
        ->whereMonth('data', $mesAtual)
        ->sum('valor');
    $saidasMesAtual = Saidas::where('idCliente', $request->idCliente)
        ->whereYear('data', $anoAtual)
        ->whereMonth('data', $mesAtual)
        ->sum('valor');
    $saldoMesAtual = $entradasMesAtual - $saidasMesAtual;

    // Saldo do mês passado
    $entradasMesPassado = Entradas::where('idCliente', $request->idCliente)
        ->whereYear('data', $anoAtual)
        ->whereMonth('data', $mesPassado)
        ->sum('valor');
    $saidasMesPassado = Saidas::where('idCliente', $request->idCliente)
        ->whereYear('data', $anoAtual)
        ->whereMonth('data', $mesPassado)
        ->sum('valor');
    $saldoMesPassado = $entradasMesPassado - $saidasMesPassado;

    // Comparação percentual entre os saldos
    $percentualSaldo = $saldoMesPassado > 0
        ? (($saldoMesAtual - $saldoMesPassado) / $saldoMesPassado) * 100
        : ($saldoMesAtual > 0 ? 100 : 0);
    $percentualSaldo = round($percentualSaldo, 2);

    // Próximos eventos
    $hoje = now();
    $proximosEventos = Eventos::where('idCliente', $request->idCliente)
        ->where('dataEvento', '>=', $hoje)
        ->with(['local'])
        ->orderBy('dataEvento')
        ->take(3)
        ->get();

    // Balanço fiscal
    $entradasPorMes = [];
    $saidasPorMes = [];
    $saldosPorMes = [];
    $nomeMeses = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];

    for ($mes = 1; $mes <= 12; $mes++) {
        $entradas = Entradas::where('idCliente', $request->idCliente)
            ->whereYear('data', $anoAtual)
            ->whereMonth('data', $mes)
            ->sum('valor');
        $saidas = Saidas::where('idCliente', $request->idCliente)
            ->whereYear('data', $anoAtual)
            ->whereMonth('data', $mes)
            ->sum('valor');
        $saldo = $entradas - $saidas;

        $entradasPorMes[$mes] = $entradas;
        $saidasPorMes[$mes] = $saidas;
        $saldosPorMes[$mes] = $saldo;
    }


    return response()->json([
        'onboarding' => [
            'userCount' => $userCount,
            'usuariosMesAtual' => $usuariosMesAtual,
            'usuariosMesPassado' => $usuariosMesPassado,
            'percentualAumento' => $percentualAumento,
            'departamentoCount' => $departamentoCount,
            'recursosCount' => $recursosCount,
        ],
        'saldoMensal' => [
            'saldoMesAtual' => $saldoMesAtual,
            'saldoMesPassado' => $saldoMesPassado,
            'percentualSaldo' => $percentualSaldo,
        ],
        'balancoFiscal' => [
            'entradas' => $entradasPorMes,
            'saidas' => $saidasPorMes,
            'meses' => $nomeMeses,
            'saldos' => $saldosPorMes,
        ],
        'eventos' => [
            'proximosEventos' => $proximosEventos,
        ],
        'saldoAtual' => [
            'saldoAtual' => $saldoMesAtual,
        ],
    ]);
}


}
