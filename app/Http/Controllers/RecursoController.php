<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recursos;
use App\Models\CategoriaRecurso;
use App\Models\TipoRecurso;
use Exception;
use Illuminate\Support\Facades\Validator;

class RecursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validando o idCliente
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        return Recursos::where('idCliente', $request->idCliente)->with(['tipo', 'categoria'])->orderBy('nomeRecurso', 'asc')->get();
    }

    public function store(Request $request)
    {
        // Validando os dados da requisição
        $validator = Validator::make($request->all(), [
            'nomeRecurso' => 'required|string|max:255',
            'tipoRecurso' => 'required|integer|exists:tipo_recursos,id', // tipoRecurso deve existir
            'categoriaRecurso' => 'required|integer|exists:categoria_recursos,id', // categoriaRecurso deve existir
            'quantidadeRecurso' => 'required|integer|min:0', // quantidadeRecurso deve ser um número maior ou igual a 0
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
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
        // Adicione validações para o recurso se necessário
    }

    public function update(Request $request, string $id)
    {
        // Validando os dados da requisição
        $validator = Validator::make($request->all(), [
            'nomeRecurso' => 'required|string|max:255',
            'tipoRecurso' => 'required|integer|exists:tipos_recurso,id',
            'categoriaRecurso' => 'required|integer|exists:categorias_recurso,id',
            'quantidadeRecurso' => 'required|integer|min:0',
            'idCliente' => 'required|integer|exists:clientes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $recurso = Recursos::findOrFail($id);
            $recurso->update([
                'nomeRecurso' => $request->nomeRecurso,
                'tipoRecurso' => $request->tipoRecurso,
                'categoriaRecurso' => $request->categoriaRecurso,
                'quantidadeRecurso' => $request->quantidadeRecurso,
                'idCliente' => $request->idCliente
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['status' => 'sucesso!', 'recurso' => $recurso], 200);
    }

    public function gerarRelatorioRecursos(Request $request)
    {


        $dataInicial = $request->dataInicial . ' 00:00:00';
        $dataFinal = $request->dataFinal . ' 23:59:59';

        // Validando o idCliente
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $data_hoje = date("Y-m-d H:i");

            $recursosCount = Recursos::where('idCliente', $request->idCliente)->whereBetween('created_at', [$dataInicial, $dataFinal])->count();
            $tipoRecursoCount = TipoRecurso::where('idCliente', $request->idCliente)->whereBetween('created_at', [$dataInicial, $dataFinal])->count();
            $categoriaRecursoCount = CategoriaRecurso::where('idCliente', $request->idCliente)->whereBetween('created_at', [$dataInicial, $dataFinal])->count();

            $tipoMaisFrequente = Recursos::select('tipoRecurso', \DB::raw('count(*) as total'))
            ->where('idCliente', $request->idCliente)
            ->whereBetween('created_at', [$dataInicial, $dataFinal])
            ->groupBy('tipoRecurso')
            ->orderBy('total', 'desc')
            ->with('tipo') // Carrega o nome do tipo relacionado
            ->first();

            $categoriaMaisFrequente = Recursos::select('categoriaRecurso', \DB::raw('count(*) as total'))
            ->where('idCliente', $request->idCliente)
            ->whereBetween('created_at', [$dataInicial, $dataFinal])
            ->groupBy('categoriaRecurso')
            ->orderBy('total', 'desc')
            ->with('categoria') // Carrega o nome da categoria relacionada
            ->first();
    

            $recursos = Recursos::where('idCliente', $request->idCliente)->with(['tipo', 'categoria'])->whereBetween('created_at', [$dataInicial, $dataFinal])->orderBy('nomeRecurso', 'asc')->get();

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
        // Adicione a lógica de exclusão com validação aqui, se necessário
    }
}
