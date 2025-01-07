<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Validator;
use App\Models\Celulas;
use App\Models\Clientes;
use App\Models\Planos;
use Exception;

class CelulasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $celulas = Celulas::with('localizacao', 'responsavel')
            ->where('idCliente', $request->idCliente)
            ->get();

        return $celulas;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    try {
        // Validando os dados de entrada
        $validator = Validator::make($request->all(), [
            'nomeCelula' => 'required|string|max:255',
            'localizacaoCelula' => 'required|integer',
            'responsavelCelula' => 'required|integer',
            'diaReuniao' => 'required|integer',
            'idCliente' => 'required|integer|exists:clientes,id',
            'imagemCelula' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

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

        // Contar o número de células existentes para o cliente
        $celulasExistentes = Celulas::where('idCliente', $request->idCliente)->count();

        // Verificar se o limite de células será ultrapassado
        if ($celulasExistentes >= $plano->qtdeCelulas) {
            throw new Exception("Limite de células atingido para o plano do cliente. Limite permitido: {$plano->qtdeCelulas}.");
        }

        // Processando a imagem, caso tenha sido enviada
        $imagePath = null;
        if ($request->hasFile('imagemCelula')) {
            $imagePath = $request->file('imagemCelula')->store('uploads', 'public');
        }

        // Criação da célula
        $celula = Celulas::create([
            'nomeCelula' => $request->nomeCelula,
            'responsavelCelula' => $request->responsavelCelula,
            'localizacaoCelula' => $request->localizacaoCelula,
            'diaReuniao' => $request->diaReuniao,
            'imagemCelula' => $imagePath,
            'idCliente' => $request->idCliente,
        ]);

        // Commit da transação
        DB::commit();

        return response()->json([
            'message' => 'Célula criada com sucesso!',
            'celula' => $celula,
        ], 201);

    } catch (Exception $e) {
        // Rollback da transação em caso de erro
        DB::rollBack();
        return response()->json([
            'message' => 'Erro ao salvar!',
            'erro' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:celulas,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $celula = Celulas::find($id);

        if (!$celula) {
            return response()->json(['message' => 'Erro! Não foi possível encontrar essa célula.'], 404);
        }

        return $celula;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nomeCelula' => 'nullable|string|max:255',
            'localizacaoCelula' => 'nullable|integer',
            'responsavelCelula' => 'nullable|integer',
            'diaReuniao' => 'nullable|integer',
            'imagemCelula' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $celula = Celulas::findOrFail($id);

            $celula->update($validator->validated());

            return response()->json(['message' => 'Célula atualizada com sucesso!', 'celula' => $celula], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao editar célula: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:celulas,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $celula = Celulas::findOrFail($id);
            $celula->delete();

            return response()->json(['message' => 'Célula deletada com sucesso!'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao deletar: ' . $e->getMessage()], 500);
        }
    }
}
