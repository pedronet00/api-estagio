<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Dizimos;
use App\Models\Entradas;
use Carbon\Carbon;
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
    // Validação dos campos de entrada
    $validator = Validator::make($request->all(), [
        'dataCulto' => 'required|date',
        'turnoCulto' => 'required|in:0,1', // Assuming turnoCulto is 0 for 'Manhã' and 1 for 'Noite'
        'valorArrecadado' => 'required|numeric|min:0',
        'idCliente' => 'required|exists:clientes,id', // Verifica se o cliente existe
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 400,
            'message' => 'Erro na validação dos dados',
            'errors' => $validator->errors(),
        ], 400);
    }

    // Verificação da data do culto
    $dataCulto = Carbon::parse($request->dataCulto);
    if ($dataCulto->isFuture()) {
        return response()->json([
            'status' => 400,
            'message' => 'Não é permitido registrar dízimos para datas futuras.',
        ], 400);
    }

    try {
        // Verificar se já existe um registro para a data e turno
        $existingDizimo = Dizimos::where('dataCulto', $request->dataCulto)
            ->where('turnoCulto', $request->turnoCulto)
            ->where('idCliente', $request->idCliente)
            ->first();

        if ($existingDizimo) {
            throw new Exception("Já existe um registro para esta data e turno!");
        }

        // Definir o turno
        $turnoCulto = $request->turnoCulto === 0 ? "Manhã" : "Noite";
        $msgEntrada = "Dízimo de {$request->dataCulto}, no culto da {$turnoCulto}";

        // Registrar o dízimo
        $dizimo = Dizimos::create([
            'dataCulto' => $request->dataCulto,
            'turnoCulto' => $request->turnoCulto,
            'valorArrecadado' => $request->valorArrecadado,
            'idCliente' => $request->idCliente
        ]);

        // Registrar a entrada
        $entrada = Entradas::create([
            'descricao' => $msgEntrada,
            'valor' => $request->valorArrecadado,
            'categoria' => 1,
            'data' => $request->dataCulto,
            'idCliente' => $request->idCliente
        ]);

        if (!$entrada) {
            throw new Exception("Erro ao registrar dízimo como entrada!");
        }

    } catch (Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Erro ao salvar registro de dízimo',
            'erro' => $e->getMessage()
        ], 500);
    }

    return response()->json([
        'status' => 200,
        'message' => 'Sucesso!',
        'dizimo' => $dizimo
    ]);
}


    public function gerarRelatorioDizimos(Request $request)
    {
        // Validação dos parâmetros de data
        $validator = Validator::make($request->all(), [
            'dataInicial' => 'required|date',
            'dataFinal' => 'required|date',
            'idCliente' => 'required|exists:clientes,id', // Verifica se o cliente existe
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Erro na validação dos dados',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            $idCliente = $request->idCliente;
            $dataInicial = $request->dataInicial . ' 00:00:00';
            $dataFinal = $request->dataFinal . ' 23:59:59';

            // Inicializando variáveis de relatórios
            $entradasPorMes = [];
            $nomeMeses = [
                'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
            ];

            $anoAtual = date('Y');

            // Filtra os dízimos com base nas datas
            $dizimos = Dizimos::where('idCliente', $idCliente)
                ->whereBetween('dataCulto', [$dataInicial, $dataFinal])
                ->orderBy('dataCulto', 'asc')
                ->get();

            if ($dizimos->isEmpty()) {
                throw new Exception("Não há registros de dízimos.");
            }

            // Calcula entradas por mês
            for ($mes = 1; $mes <= 12; $mes++) {
                $entradas = Dizimos::where('idCliente', $idCliente)
                    ->whereYear('dataCulto', $anoAtual)
                    ->whereMonth('dataCulto', $mes)
                    ->where('valorArrecadado', '>', 0)
                    ->sum('valorArrecadado');

                $entradasPorMes[$mes] = $entradas;
            }

            // Filtra meses com entradas válidas
            $entradasFiltradas = array_filter($entradasPorMes, function ($valor) {
                return $valor > 0;
            });

            if (empty($entradasFiltradas)) {
                throw new Exception("Não há entradas com valores maiores que 0.");
            }

            // Determina o mês com maior e menor entrada
            $mesMaiorEntrada = array_keys($entradasFiltradas, max($entradasFiltradas))[0];
            $valorMaiorEntrada = max($entradasFiltradas);

            $mesMenorEntrada = array_keys($entradasFiltradas, min($entradasFiltradas))[0];
            $valorMenorEntrada = min($entradasFiltradas);

        } catch (Exception $e) {
            return response()->json([
                'message' => "Erro!",
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Sucesso!',
            'entradas' => $entradasPorMes,
            'mesMaiorEntrada' => [
                'mes' => $nomeMeses[$mesMaiorEntrada - 1],
                'valor' => $valorMaiorEntrada
            ],
            'mesMenorEntrada' => [
                'mes' => $nomeMeses[$mesMenorEntrada - 1],
                'valor' => $valorMenorEntrada
            ],
            'dizimos' => $dizimos
        ]);
    }

    /**
     * Other methods (show, update, destroy, etc.) can also be validated as needed.
     */
}
