<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Missoes;
use Exception;

class MissoesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Missoes::where('idCliente', $request->idCliente)->with('pastorTitular')->get();
    }

    public function store(Request $request)
    {
        try{

            if(!$request->nomeMissao){
                throw new Exception("Você deve preencher o nome da missão!");
            }

            if(!$request->quantidadeMembros){
                throw new Exception("Você deve preencher a quantidade de membros!");
            }

            if(!$request->cidadeMissao){
                throw new Exception("Você deve preencher a cidade da missão!");
            }

            if(!$request->pastorTitular){
                throw new Exception("Você deve preencher o pastor titular da missão!");
            }

            $missao = Missoes::create([
                'nomeMissao' => $request->nomeMissao,
                'quantidadeMembros' => $request->quantidadeMembros,
                'cidadeMissao' => $request->cidadeMissao,
                'pastorTitular' => $request->pastorTitular,
                'statusMissao' => 1,
                'idCliente' => $request->idCliente
            ]);

        } catch(Exception $e){
            return response()->json(['error' => 'Erro ao buscar missão: '. $e->getMessage()], 404);
        }

        return response()->json(['message' => 'Missão criada com sucesso!', 'missao' => $missao], 201);
    }

    public function show(string $id)
    {
        try{
            
            $missao = Missoes::find($id);

            if(!$missao){
                throw new Exception("Missão não encontrada!");
            }

        } catch(Exception $e){
            return response()->json(['error' => 'Erro ao buscar missão: '. $e->getMessage()], 404);
        }

        return response()->json(['missao' => $missao], 200);
    }

    public function update(Request $request, string $id)
    {
        try{
            $missao = Missoes::find($id);

            if(!$missao){
                throw new Exception("Missão não encontrada!");
            }

            $missao->nomeMissao = $request->nomeMissao?? $missao->nomeMissao;
            $missao->quantidadeMembros = $request->quantidadeMembros?? $missao->quantidadeMembros;
            $missao->cidadeMissao = $request->cidadeMissao?? $missao->cidadeMissao;
            $missao->pastorTitular = $request->pastorTitular?? $missao->pastorTitular;
            $missao->save();

        } catch(Exception $e)
        {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }

        return response()->json(['message'=> 'Missão atualizada com sucesso!', 'missao' => $missao], 200);
    }

    public function deactivate(string $id){
        try{

            if(!$id){
                throw new Exception("ID não informado!");
            }

            $missao = Missoes::find($id);

            if(!$missao){
                throw new Exception("Missão não encontrada!");
            }

            if($missao->statusMissao == 0){
                throw new Exception("Missão já está desativada!");
            }

            $missao->statusMissao = 0;
            $missao->save();

        } catch(Exception $e){
            return response()->json(['message' => 'Erro ao desativar a missão', 'error'=> $e->getMessage()]);
        }

        return response()->json(['message' => 'Missão desativada com sucesso!'], 200);
    }

    public function activate(string $id){
        try{

            if(!$id){
                throw new Exception("ID não informado!");
            }

            $missao = Missoes::find($id);

            if(!$missao){
                throw new Exception("Missão não encontrada!");
            }

            if($missao->statusMissao == 1){
                throw new Exception("Missão já está ativada!");
            }

            $missao->statusMissao = 1;
            $missao->save();

        } catch(Exception $e){
            return response()->json(['message' => 'Erro ao ativar a missão', 'error'=> $e->getMessage()]);
        }

        return response()->json(['message' => 'Missão ativada com sucesso!'], 200);
    }

    public function gerarRelatorioMissoes(Request $request)
    {
        try{

            $data_hoje = date("Y-m-d H:i");

            $missoesCount = Missoes::where('idCliente', $request->idCliente)->count();
            $missoessAtivas = Missoes::where('idCliente', $request->idCliente)->where('statusMissao', 1)->count();
            $missoessInativas = Missoes::where('idCliente', $request->idCliente)->where('statusMissao', 0)->count();
            $membrosCount = Missoes::where('idCliente', $request->idCliente)->sum('quantidadeMembros');
            $missoes = Missoes::where('idCliente', $request->idCliente)->with('pastorTitular')->orderBy('nomeMissao', 'asc')->get();

            if(!$missoes){
                throw new Exception("Nenhuma missão encontrada!");
            }

        } catch(Exception $e){
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
            ],200
        );
    }

    public function destroy(string $id)
    {
        //
    }
}
