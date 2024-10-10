<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recursos;
use App\Models\CategoriaRecurso;
use App\Models\TipoRecurso;
use Exception;

class RecursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Recursos::where('idCliente', $request->idCliente)->with(['tipo', 'categoria'])->orderBy('nomeRecurso', 'asc')->get();
    }

    public function store(Request $request)
    {
        try {
            if (!$request->nomeRecurso) {
                throw new Exception("Você deve preencher o nome do recurso!");
            }

            if (!$request->tipoRecurso) {
                throw new Exception("Você deve preencher o tipo do recurso!");
            }

            if (!$request->categoriaRecurso) {
                throw new Exception("Você deve preencher a categoria do recurso!");
            }

            if (!$request->quantidadeRecurso) {
                throw new Exception("Você deve preencher a quantidade do recurso!");
            }

            $recurso = Recursos::create([
                'nomeRecurso' => $request->nomeRecurso,
                'tipoRecurso' => $request->tipoRecurso,
                'categoriaRecurso' => $request->categoriaRecurso,
                'quantidadeRecurso' => $request->quantidadeRecurso,
                'idCliente' => $request->idCliente
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['status' => 'sucesso!', 'recurso' => $recurso], 201);
    }

    public function aumentarQuantidade(string $id)
    {
        try {
            $recurso = Recursos::findOrFail($id);
    
            // Aumenta a quantidade do recurso em 1
            $recurso->quantidadeRecurso += 1;
            $recurso->save();
    
            return response()->json(['status' => 'sucesso!', 'novaQuantidade' => $recurso->quantidadeRecurso], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    public function diminuirQuantidade(string $id)
    {
        try {
            $recurso = Recursos::findOrFail($id);
    
            if ($recurso->quantidadeRecurso <= 0) {
                throw new Exception("Não é possível diminuir a quantidade, pois ela já é zero.");
            }
    
            // Diminui a quantidade do recurso em 1
            $recurso->quantidadeRecurso -= 1;
            $recurso->save();
    
            return response()->json(['status' => 'sucesso!', 'novaQuantidade' => $recurso->quantidadeRecurso], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function gerarRelatorioRecursos(Request $request){
        try{

            $data_hoje = date("Y-m-d H:i");

            $recursosCount = Recursos::where('idCliente', $request->idCliente)->count();
            $tipoRecursoCount = TipoRecurso::where('idCliente', $request->idCliente)->count();
            $categoriaRecursoCount = CategoriaRecurso::where('idCliente', $request->idCliente)->count();

            $tipoMaisFrequente = Recursos::select('tipoRecurso', \DB::raw('count(*) as total'))
            ->where('idCliente', $request->idCliente)
            ->groupBy('tipoRecurso')
            ->orderBy('total', 'desc')
            ->with('tipo') // Carrega o nome do tipo relacionado
            ->first();

            $categoriaMaisFrequente = Recursos::select('categoriaRecurso', \DB::raw('count(*) as total'))
            ->where('idCliente', $request->idCliente)
            ->groupBy('categoriaRecurso')
            ->orderBy('total', 'desc')
            ->with('categoria') // Carrega o nome da categoria relacionada
            ->first();
    

            $recursos = Recursos::where('idCliente', $request->idCliente)->with(['tipo', 'categoria'])->orderBy('nomeRecurso', 'asc')->get();

            if(!$recursos){
                throw new Exception("Nenhum recurso encontrado!");
            }

        } catch(Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }

        return response()->json([
            'message' => 'Relatório gerado com sucesso!', 
            'titulo' => 'Relatório de Recursos da Primeira Igreja Batista de Presidente Prudente', 
            'qtdeRecursos' => $recursosCount,
            'qtdeTipoRecursos' => $tipoRecursoCount,
            'qtdeCategoriaRecursos' => $categoriaRecursoCount,
            'tipoMaisFrequente' => $tipoMaisFrequente,
            'categoriaMaisFrequente' => $categoriaMaisFrequente,
            'recursos' => $recursos, 
            'data' => $data_hoje
            ],200
        );
    }

    public function destroy(string $id)
    {
        //
    }
}
