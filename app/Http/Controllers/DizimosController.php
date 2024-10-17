<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dizimos;
use Exception;

class DizimosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Dizimos::where('idCliente', $request->idCliente)->get();
    }

    public function store(Request $request)
    {
        try {
            // Validações de campos obrigatórios
            if (!$request->dataCulto) {
                throw new Exception("A data do culto não pode estar vazia!");
            }

            // if (!$request->turnoCulto) {
            //     throw new Exception("O turno do culto não pode estar vazio!");
            // }

            if (!$request->valorArrecadado) {
                throw new Exception("O valor arrecadado não pode estar vazio!");
            }

            // Verifica se já existe um registro com a mesma data e turno
            $existingDizimo = Dizimos::where('dataCulto', $request->dataCulto)
                ->where('turnoCulto', $request->turnoCulto)
                ->first();

            if ($existingDizimo) {
                throw new Exception("Já existe um registro para esta data e turno!");
            }

            // Criação do novo registro
            $dizimo = Dizimos::create([
                'dataCulto' => $request->dataCulto,
                'turnoCulto' => $request->turnoCulto,
                'valorArrecadado' => $request->valorArrecadado,
                'idCliente' => $request->idCliente
            ]);

        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Erro ao salvar registro de dízimo', 'erro' => $e->getMessage()]);
        }

        return response()->json(['status' => 200, 'message' => 'Sucesso!', 'dizimo' => $dizimo]);
    }


    public function gerarRelatorioDizimos(Request $request)
    {

        try{
            $idCliente = $request->idCliente;

            $entradasPorMes = [];
            $nomeMeses = [
                'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
            ];

            $anoAtual = date('Y');

            $dizimos = Dizimos::where('idCliente', $idCliente)->get();

            if($dizimos == "[]"){
                throw new Exception("Não há registros de dízimos.");
            }

            for ($mes = 1; $mes <= 12; $mes++) {
                // Somar as entradas e saídas do mês atual
                $entradas = Dizimos::where('idCliente', $idCliente)
                    ->whereYear('dataCulto', $anoAtual)
                    ->whereMonth('dataCulto', $mes)
                    ->sum('valorArrecadado'); // Altere 'valor' para o campo correto

                // Armazenar os valores nos arrays
                $entradasPorMes[$mes] = $entradas;
            }
        } catch(Exception $e){
            return response()->json([
                'message' => "Erro!",
                'error' => $e->getMessage()
            ]);
        }

        return response()->json([
            'message' => 'Sucesso!',
            'entradas' => $entradas,
            'dizimos' => $dizimos,
        ]);
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
