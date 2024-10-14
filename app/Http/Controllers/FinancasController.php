<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse; // Certificando de usar o namespace correto
use Illuminate\Http\Request;
use App\Models\Entradas;
use App\Models\Saidas;
use App\Models\Financas;


class FinancasController extends Controller
{

    public function saldoMensal(Request $request): JsonResponse
    {
        // Pegando o idCliente da query string
        $idCliente = $request->query('idCliente');

        // Verificando se o idCliente foi passado
        if (!$idCliente) {
            return response()->json(['error' => 'O parâmetro idCliente é obrigatório.'], 400);
        }

        // Calculando o saldo mensal para o cliente
        $saldoMensal = Financas::calcularSaldoMensal($idCliente);

        // Retornando o saldo em formato JSON
        return response()->json([
            'saldoMensal' => $saldoMensal
        ], 200);
    }

    public function entradasSaidasMensais(Request $request)
    {
        $idCliente = $request->idCliente;

        // Obter o ano atual
        $anoAtual = date('Y');

        // Inicializar arrays para armazenar entradas e saídas por mês
        $entradasPorMes = [];
        $saidasPorMes = [];
        $nomeMeses = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];

        // Loop pelos meses de 1 a 12
        for ($mes = 1; $mes <= 12; $mes++) {
            // Somar as entradas e saídas do mês atual
            $entradas = Entradas::where('idCliente', $idCliente)
                ->whereYear('data', $anoAtual)
                ->whereMonth('data', $mes)
                ->sum('valor'); // Altere 'valor' para o campo que contém o valor da entrada

            $saidas = Saidas::where('idCliente', $idCliente)
                ->whereYear('data', $anoAtual)
                ->whereMonth('data', $mes)
                ->sum('valor'); // Altere 'valor' para o campo que contém o valor da saída

            // Armazenar os valores nos arrays
            $entradasPorMes[$mes] = $entradas;
            $saidasPorMes[$mes] = $saidas;
        }

        return response()->json([
            'entradas' => $entradasPorMes,
            'saidas' => $saidasPorMes,
            'meses' => $nomeMeses
        ]);
    }

    public function gerarRelatorioFinancas(Request $request): JsonResponse
    {
        // Pegando o idCliente da query string
        $idCliente = $request->idCliente;

        // Verificando se o idCliente foi passado
        if (!$idCliente) {
            return response()->json(['error' => 'O parâmetro idCliente é obrigatório.'], 400);
        }

        // Obtendo o saldo mensal atual
        $saldoMensal = Financas::calcularSaldoMensal($idCliente);

        // Inicializando arrays para armazenar entradas e saídas por mês
        $entradasPorMes = [];
        $saidasPorMes = [];
        $nomeMeses = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];

        // Obter o ano atual
        $anoAtual = date('Y');

        // Loop pelos meses de 1 a 12
        for ($mes = 1; $mes <= 12; $mes++) {
            // Somar as entradas e saídas do mês atual
            $entradas = Entradas::where('idCliente', $idCliente)
                ->whereYear('data', $anoAtual)
                ->whereMonth('data', $mes)
                ->sum('valor'); // Altere 'valor' para o campo correto

            $saidas = Saidas::where('idCliente', $idCliente)
                ->whereYear('data', $anoAtual)
                ->whereMonth('data', $mes)
                ->sum('valor'); // Altere 'valor' para o campo correto

            // Armazenar os valores nos arrays
            $entradasPorMes[$mes] = $entradas;
            $saidasPorMes[$mes] = $saidas;
        }

        // Identificar o mês com mais entradas e mais saídas
        $mesMaiorEntrada = array_keys($entradasPorMes, max($entradasPorMes))[0]; // Retorna o mês com maior entrada
        $valorMaiorEntrada = max($entradasPorMes); // Retorna o valor da maior entrada

        $mesMaiorSaida = array_keys($saidasPorMes, max($saidasPorMes))[0]; // Retorna o mês com maior saída
        $valorMaiorSaida = max($saidasPorMes); // Retorna o valor da maior saída

        // Retornar os dados em formato JSON
        return response()->json([
            'saldoMensalAtual' => $saldoMensal,
            'entradas' => $entradasPorMes,
            'saidas' => $saidasPorMes,
            'mesMaiorEntrada' => [
                'mes' => $nomeMeses[$mesMaiorEntrada - 1], // Converter o índice do mês
                'valor' => $valorMaiorEntrada
            ],
            'mesMaiorSaida' => [
                'mes' => $nomeMeses[$mesMaiorSaida - 1], // Converter o índice do mês
                'valor' => $valorMaiorSaida
            ],
            'meses' => $nomeMeses
        ]);
    }

    
}
