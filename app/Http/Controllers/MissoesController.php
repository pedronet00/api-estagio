<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Missoes;
use App\Models\Clientes;
use App\Models\Planos;
use Exception;

class MissoesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validando a entrada
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente é obrigatório e deve existir
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        return Missoes::where('idCliente', $request->idCliente)->with('pastorTitular')->get();
    }

    public function store(Request $request)
{
    // Validando os dados de entrada
    $validator = Validator::make($request->all(), [
        'nomeMissao' => 'required|string|max:255',
        'quantidadeMembros' => 'required|integer',
        'cidadeMissao' => 'required|string|max:255',
        'pastorTitular' => 'required|integer',
        'idCliente' => 'required|integer|exists:clientes,id', // idCliente obrigatório e deve existir
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    try {
        // Iniciando a transação
        DB::beginTransaction();

        // Verificar se o cliente existe
        $cliente = Clientes::find($request->idCliente);
        if (!$cliente) {
            throw new Exception("Cliente não encontrado!");
        }

        // Buscar o plano do cliente
        $plano = Planos::find($cliente->idPlano);
        if (!$plano) {
            throw new Exception("Plano do cliente não encontrado!");
        }

        // Contar o número de missões existentes para o cliente
        $missoesExistentes = Missoes::where('idCliente', $request->idCliente)->count();

        // Verificar se o limite de missões será ultrapassado
        if ($missoesExistentes >= $plano->qtdeMissoes) {
            throw new Exception("Limite de missões atingido para o plano do cliente. Limite permitido: {$plano->qtdeMissoes}.");
        }

        // Criação da missão
        $missao = Missoes::create([
            'nomeMissao' => $request->nomeMissao,
            'quantidadeMembros' => $request->quantidadeMembros,
            'cidadeMissao' => $request->cidadeMissao,
            'pastorTitular' => $request->pastorTitular,
            'statusMissao' => 1,
            'idCliente' => $request->idCliente,
        ]);

        // Commit da transação
        DB::commit();

        return response()->json([
            'message' => 'Missão criada com sucesso!',
            'missao' => $missao,
        ], 201);

    } catch (Exception $e) {
        // Rollback da transação em caso de erro
        DB::rollBack();
        return response()->json([
            'error' => 'Erro ao cadastrar missão: ' . $e->getMessage(),
        ], 400);
    }
}


    public function show(Request $request)
    {
        // Validando o ID
        $validator = Validator::make(['id' => $request->id], [
            'id' => 'required|exists:missoes,id', // ID obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $missao = Missoes::find($request->id);

            if (!$missao) {
                throw new Exception("Missão não encontrada!");
            }

            if($missao->idCliente != $request->idCliente){
                return response()->json(['error' => 'Você não pode acessar essa missão.', 'idClienteMissao' => $missao->idCliente, 'idClienteRequest' => $request->idCliente], 403);
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao buscar missão: '. $e->getMessage()], 404);
        }

        return response()->json(['missao' => $missao], 200);
    }

    public function update(Request $request, string $id)
    {
        // Validando os dados de entrada para atualização
        $validator = Validator::make($request->all(), [
            'nomeMissao' => 'nullable|string|max:255',
            'quantidadeMembros' => 'nullable|integer',
            'cidadeMissao' => 'nullable|string|max:255',
            'pastorTitular' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $missao = Missoes::find($id);

            if (!$missao) {
                throw new Exception("Missão não encontrada!");
            }

            $missao->nomeMissao = $request->nomeMissao ?? $missao->nomeMissao;
            $missao->quantidadeMembros = $request->quantidadeMembros ?? $missao->quantidadeMembros;
            $missao->cidadeMissao = $request->cidadeMissao ?? $missao->cidadeMissao;
            $missao->pastorTitular = $request->pastorTitular ?? $missao->pastorTitular;
            $missao->save();

        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }

        return response()->json(['message'=> 'Missão atualizada com sucesso!', 'missao' => $missao], 200);
    }

    public function deactivate(string $id)
    {
        // Validando o ID
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:missoes,id', // ID obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $missao = Missoes::find($id);

            if (!$missao) {
                throw new Exception("Missão não encontrada!");
            }

            if ($missao->statusMissao == 0) {
                throw new Exception("Missão já está desativada!");
            }

            $missao->statusMissao = 0;
            $missao->save();

        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao desativar a missão', 'error'=> $e->getMessage()]);
        }

        return response()->json(['message' => 'Missão desativada com sucesso!'], 200);
    }

    public function activate(string $id)
    {
        // Validando o ID
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:missoes,id', // ID obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $missao = Missoes::find($id);

            if (!$missao) {
                throw new Exception("Missão não encontrada!");
            }

            if ($missao->statusMissao == 1) {
                throw new Exception("Missão já está ativada!");
            }

            $missao->statusMissao = 1;
            $missao->save();

        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao ativar a missão', 'error'=> $e->getMessage()]);
        }

        return response()->json(['message' => 'Missão ativada com sucesso!'], 200);
    }

    public function gerarRelatorioMissoes(Request $request)
    {

        $dataInicial = $request->dataInicial . ' 00:00:00';
        $dataFinal = $request->dataFinal . ' 23:59:59';

        // Validando a entrada
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente obrigatório e deve existir
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $data_hoje = date("Y-m-d H:i");

            $missoesCount = Missoes::where('idCliente', $request->idCliente)->whereBetween('created_at', [$dataInicial, $dataFinal])->count();
            $missoessAtivas = Missoes::where('idCliente', $request->idCliente)->where('statusMissao', 1)->whereBetween('created_at', [$dataInicial, $dataFinal])->count();
            $missoessInativas = Missoes::where('idCliente', $request->idCliente)->where('statusMissao', 0)->whereBetween('created_at', [$dataInicial, $dataFinal])->count();
            $membrosCount = Missoes::where('idCliente', $request->idCliente)->whereBetween('created_at', [$dataInicial, $dataFinal])->sum('quantidadeMembros');
            $missoes = Missoes::where('idCliente', $request->idCliente)->with('pastorTitular')->whereBetween('created_at', [$dataInicial, $dataFinal])->orderBy('nomeMissao', 'asc')->get();

            if (!$missoes) {
                throw new Exception("Nenhuma missão encontrada!");
            }

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }

        return response()->json([
            'message' => 'Relatório gerado com sucesso!',
            'titulo' => 'Relatório das Missões da Primeira Igreja Batista de Presidente Prudente',
            'qtdeMissoes' => $missoesCount,
            'qtdeMissoesAtivas' => $missoessAtivas,
            'qtdeMissoesInativas' => $missoessInativas,
            'qtdeMembrosMissoes' => $membrosCount,
            'missoes' => $missoes,
            'data' => $data_hoje
        ], 200);
    }

    public function destroy(string $id)
    {
        // Este método pode ser implementado conforme necessário
    }
}
