<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Entradas;
use App\Models\Saidas;
use App\Models\Financas;

class FinancasController extends Controller
{
    public function saldoMensal(Request $request): JsonResponse
    {
        // Validando o parâmetro idCliente
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id', // Verifica se idCliente é obrigatório, é um número inteiro e existe na tabela clientes
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Pegando o idCliente da query string
        $idCliente = $request->query('idCliente');

        // Calculando o saldo mensal para o cliente
        $saldoMensal = Financas::calcularSaldoMensal($idCliente);

        return response()->json([
            'saldoMensal' => $saldoMensal
        ], 200);
    }

    public function entradasSaidasMensais(Request $request)
    {
        // Validando o parâmetro idCliente
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id', // Verifica se idCliente é obrigatório, é um número inteiro e existe na tabela clientes
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $idCliente = $request->idCliente;
        $anoAtual = date('Y');
        $entradasPorMes = [];
        $saidasPorMes = [];
        $saldosPorMes = [];
        $nomeMeses = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];

        for ($mes = 1; $mes <= 12; $mes++) {
            $entradas = Entradas::where('idCliente', $idCliente)
                ->whereYear('data', $anoAtual)
                ->whereMonth('data', $mes)
                ->sum('valor');

            $saidas = Saidas::where('idCliente', $idCliente)
                ->whereYear('data', $anoAtual)
                ->whereMonth('data', $mes)
                ->sum('valor');

            $saldo = $entradas - $saidas;

            $entradasPorMes[$mes] = $entradas;
            $saidasPorMes[$mes] = $saidas;
            $saldosPorMes[$mes] = $saldo;
        }

        return response()->json([
            'entradas' => $entradasPorMes,
            'saidas' => $saidasPorMes,
            'saldos' => $saldosPorMes,
            'meses' => $nomeMeses
        ]);
    }

    public function gerarRelatorioFinancas(Request $request): JsonResponse
{
    $dataInicial = $request->dataInicial;
    $dataFinal = $request->dataFinal;

    // Validando o parâmetro idCliente
    $validator = Validator::make($request->all(), [
        'idCliente' => 'required|integer|exists:clientes,id', // Verifica se idCliente é obrigatório, é um número inteiro e existe na tabela clientes
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    $idCliente = $request->idCliente;
    $nomeMeses = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];

    $entradasPorMes = [];
    $saidasPorMes = [];
    $saldosPorMes = [];

    // Inicializa as variáveis para armazenar as somas totais
    $totalEntradas = 0;
    $totalSaidas = 0;

    // Transformando as datas em objetos Carbon para facilitar a manipulação
    $dataInicial = \Carbon\Carbon::parse($dataInicial);
    $dataFinal = \Carbon\Carbon::parse($dataFinal);

    // Criando um array para os meses no intervalo
    $mesesNoIntervalo = [];

    // Loop por cada mês entre a dataInicial e dataFinal
    $currentDate = $dataInicial->copy();
    while ($currentDate <= $dataFinal) {
        $mes = $currentDate->month;
        $ano = $currentDate->year;

        // Adicionando o mês ao array de meses dentro do intervalo
        $mesesNoIntervalo[] = $mes;

        // Somando entradas do mês
        $entradas = Entradas::where('idCliente', $idCliente)
            ->whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->sum('valor');
        
        // Somando saídas do mês
        $saidas = Saidas::where('idCliente', $idCliente)
            ->whereYear('data', $ano)
            ->whereMonth('data', $mes)
            ->sum('valor');

        // Calculando o saldo para esse mês
        $saldo = $entradas - $saidas;

        // Armazenando as informações de cada mês
        $entradasPorMes[$mes] = $entradas;
        $saidasPorMes[$mes] = $saidas;
        $saldosPorMes[$mes] = $saldo;

        // Acumulando as somas totais de entradas e saídas
        $totalEntradas += $entradas;
        $totalSaidas += $saidas;

        // Avançando para o próximo mês
        $currentDate->addMonth();
    }

    // Calculando o saldo total acumulado
    $saldoMensalAtual = $totalEntradas - $totalSaidas;

    // Encontrando o mês com maior entrada e maior saída
    $mesMaiorEntrada = array_keys($entradasPorMes, max($entradasPorMes))[0];
    $valorMaiorEntrada = max($entradasPorMes);

    $mesMaiorSaida = array_keys($saidasPorMes, max($saidasPorMes))[0];
    $valorMaiorSaida = max($saidasPorMes);

    return response()->json([
        'saldoMensalAtual' => $saldoMensalAtual, // Corrigido para o valor correto
        'entradas' => $entradasPorMes,
        'saidas' => $saidasPorMes,
        'saldos' => $saldosPorMes,
        'mesMaiorEntrada' => [
            'mes' => $nomeMeses[$mesMaiorEntrada - 1],
            'valor' => $valorMaiorEntrada
        ],
        'mesMaiorSaida' => [
            'mes' => $nomeMeses[$mesMaiorSaida - 1],
            'valor' => $valorMaiorSaida
        ],
        'meses' => array_map(function($mes) use ($nomeMeses) {
            return $nomeMeses[$mes - 1]; // Mapear o número do mês para o nome
        }, $mesesNoIntervalo)
    ]);
}

}
