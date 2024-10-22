<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dizimos;
use App\Models\Entradas;
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
            if (!$request->dataCulto) {
                throw new Exception("A data do culto não pode estar vazia!");
            }

            // if (!$request->turnoCulto) {
            //     throw new Exception("O turno do culto não pode estar vazio!");
            // }

            if (!$request->valorArrecadado) {
                throw new Exception("O valor arrecadado não pode estar vazio!");
            }

            $existingDizimo = Dizimos::where('dataCulto', $request->dataCulto)
                ->where('turnoCulto', $request->turnoCulto)
                ->where('idCliente', $request->idCliente)
                ->first();

            if ($existingDizimo) {
                throw new Exception("Já existe um registro para esta data e turno!");
            }

            $dataCulto = $request->dataCulto;
            $turnoCulto = $request->turnoCulto === 0 ? "Manhã" : "Noite";

            $msgEntrada = "Dízimo de $dataCulto, no culto da $turnoCulto";

            $dizimo = Dizimos::create([
                'dataCulto' => $request->dataCulto,
                'turnoCulto' => $request->turnoCulto,
                'valorArrecadado' => $request->valorArrecadado,
                'idCliente' => $request->idCliente
            ]);

            $entrada = Entradas::create([
                'descricao' => $msgEntrada,
                'valor' => $request->valorArrecadado,
                'categoria' => 1,
                'data' => $request->dataCulto,
                'idCliente' => $request->idCliente
            ]);

            if(!$entrada){
                throw new Exception("Erro ao registrar dízimo como entrada!");
            }

        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Erro ao salvar registro de dízimo', 'erro' => $e->getMessage()]);
        }

        return response()->json(['status' => 200, 'message' => 'Sucesso!', 'dizimo' => $dizimo]);
    }


    public function gerarRelatorioDizimos(Request $request)
    {
        try {
            $idCliente = $request->idCliente;

            $entradasPorMes = [];
            $nomeMeses = [
                'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
            ];

            $anoAtual = date('Y');

            $dizimos = Dizimos::where('idCliente', $idCliente)->orderBy('dataCulto', 'asc')->get();

            $cultoMaiorValorDizimo = Dizimos::where('idCliente', $idCliente)->orderBy('valorArrecadado', 'desc')->first();
            $cultoMenorValorDizimo = Dizimos::where('idCliente', $idCliente)->orderBy('valorArrecadado', 'asc')->first();

            if ($dizimos->isEmpty()) {
                throw new Exception("Não há registros de dízimos.");
            }

            for ($mes = 1; $mes <= 12; $mes++) {
                // Somar as entradas do mês atual
                $entradas = Dizimos::where('idCliente', $idCliente)
                    ->whereYear('dataCulto', $anoAtual)
                    ->whereMonth('dataCulto', $mes)
                    ->where('valorArrecadado', '>', 0)
                    ->sum('valorArrecadado');

                // Armazenar os valores nos arrays
                $entradasPorMes[$mes] = $entradas;
            }

            // Filtrar os meses com valores maiores que 0
            $entradasFiltradas = array_filter($entradasPorMes, function ($valor) {
                return $valor > 0;
            });

            // Verificar se existem entradas válidas
            if (empty($entradasFiltradas)) {
                throw new Exception("Não há entradas com valores maiores que 0.");
            }

            $mesMaiorEntrada = array_keys($entradasFiltradas, max($entradasFiltradas))[0]; // Retorna o mês com maior entrada
            $valorMaiorEntrada = max($entradasFiltradas); // Retorna o valor da maior entrada

            $mesMenorEntrada = array_keys($entradasFiltradas, min($entradasFiltradas))[0]; // Retorna o mês com menor entrada
            $valorMenorEntrada = min($entradasFiltradas); // Retorna o valor da menor entrada

        } catch (Exception $e) {
            return response()->json([
                'message' => "Erro!",
                'error' => $e->getMessage()
            ]);
        }

        return response()->json([
            'message' => 'Sucesso!',
            'entradas' => $entradasPorMes,
            'cultoMaiorValorArrecadado' => $cultoMaiorValorDizimo,
            'cultoMenorValorArrecadado' => $cultoMenorValorDizimo,
            'mesMaiorEntrada' => [
                'mes' => $nomeMeses[$mesMaiorEntrada - 1], // Converter o índice do mês
                'valor' => $valorMaiorEntrada
            ],
            'mesMenorEntrada' => [
                'mes' => $nomeMeses[$mesMenorEntrada - 1], // Converter o índice do mês
                'valor' => $valorMenorEntrada
            ],
            'dizimos' => $dizimos
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
