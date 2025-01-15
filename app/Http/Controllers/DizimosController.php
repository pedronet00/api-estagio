<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Dizimos;
use App\Models\Entradas;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class DizimosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Dizimos::where('idCliente', $request->idCliente)->get();
    }

    public function show(string $id){

        try{

            $dizimo = Dizimos::findOrFail($id);

            if(!$dizimo){
                return response()->json(['erro' => 'Dízimo não encontrado'], 422);
            }

        } catch(Exception $e){
            return response()->json(['erro' => $e->getMessage()]);
        }

        return $dizimo;
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
                'idCliente' => $request->idCliente,
                'idEntrada' => 999999
            ]);

            // Registrar a entrada
            $entrada = Entradas::create([
                'descricao' => $msgEntrada,
                'valor' => $request->valorArrecadado,
                'categoria' => 1,
                'data' => $request->dataCulto,
                'idCliente' => $request->idCliente,
                
            ]);

            if (!$entrada) {
                throw new Exception("Erro ao registrar dízimo como entrada!");
            }

            $dizimo->idEntrada = $entrada->id;
            $dizimo->save();

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

    public function update(Request $request, string $id){

        try{

            $dizimo = Dizimos::findOrFail($id);

            if(!$dizimo){
                return response()->json(['erro' => 'Dízimo não encontrado'], 500);
            }

            $dizimo->dataCulto = $request->dataCulto ?? $dizimo->dataCulto;
            $dizimo->turnoCulto = $request->turnoCulto ?? $dizimo->turnoCulto;
            $dizimo->valorArrecadado = $request->valorArrecadado ?? $dizimo->valorArrecadado;
            
            $entrada = Entradas::findOrFail($dizimo->idEntrada);

            if(!$entrada){
                return response()->json(['erro' => 'Entrada não encontrada'], 500);
            }

            if($dizimo->turnoCulto == 0){
                $turno = "Manhã";
            } else{
                $turno = "Noite";
            }

            $msg = "Dízimo de $dizimo->dataCulto, no turno da $turno";

            $entrada->data = $request->dataCulto ?? $entrada->data;
            $entrada->valor = $request->valorArrecadado ??$entrada->valor;
            $entrada->descricao = $msg;

            $dizimo->save();
            $entrada->save();

        } catch(Exception $e){
            return response()->json(['erro' => $e->getMessage()]);
        }

        return response()->json(['sucesso' => "Dízimo e entrada atualizados com sucesso!"]);

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

    public function destroy($id)
    {
        // Iniciar uma transação para garantir que ambos os registros sejam excluídos juntos
        DB::beginTransaction();

        try {
            // Buscar o registro de dízimo pelo ID
            $dizimo = Dizimos::findOrFail($id);

            if($dizimo->turnoCulto == 0){
                $turnoCulto = "Manhã";
            } else{
                $turnoCulto = "Noite";
            }

            // Localizar a entrada correspondente ao dízimo
            $entrada = Entradas::where('descricao', 'like', "Dízimo de {$dizimo->dataCulto}, no culto da {$turnoCulto}")
                ->where('idCliente', $dizimo->idCliente)
                ->first();

            // Excluir o dízimo
            $dizimo->delete();

            // Excluir a entrada correspondente se existir
            if (!$entrada) {
                return response()->json(['erro' => 'entrada não encontrada', 'msg' => $msg], 422);
            }
            
            $entrada->delete();
            // Confirmar a transação
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Dízimo e entrada correspondente excluídos com sucesso!'
            ]);
        } catch (Exception $e) {
            // Reverter a transação em caso de erro
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'Erro ao excluir o dízimo',
                'erro' => $e->getMessage()
            ], 500);
        }
    }
}
