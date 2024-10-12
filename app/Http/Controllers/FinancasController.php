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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

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


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
