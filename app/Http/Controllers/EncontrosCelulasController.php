<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EncontrosCelulas;
use Illuminate\Support\Facades\Validator;

class EncontrosCelulasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer',
            'idCelula' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        return EncontrosCelulas::where('idCliente', $request->idCliente)
            ->where('idCelula', $request->idCelula)
            ->with('celula', 'localizacao', 'responsavel')
            ->orderBy('dataEncontro', 'desc')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validação
        $validator = Validator::make($request->all(), [
            'idCelula' => 'required|integer',
            'idLocal' => 'required|integer',
            'idPessoaEstudo' => 'required|integer',
            'idCliente' => 'required|integer',
            'dataEncontro' => 'required|date', 
            'qtdePresentes' => 'nullable|integer',
            'temaEstudo' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $encontroCelula = EncontrosCelulas::create([
                'idCelula' => $request->idCelula,
                'idLocal' => $request->idLocal,
                'idPessoaEstudo' => $request->idPessoaEstudo,
                'dataEncontro' => $request->dataEncontro,
                'qtdePresentes' => $request->qtdePresentes,
                'temaEstudo' => $request->temaEstudo,
                'idCliente' => $request->idCliente
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao salvar!', 'erro' => $e->getMessage()]);
        }

        return response()->json([
            'message' => 'Encontro da célula criado com sucesso!',
            'detalhes' => $encontroCelula,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Validação se o ID é válido (opcional)
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:encontros_celulas,id',  // Verifica se o encontro existe
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Lógica para exibir o recurso específico
    }

    public function proximoEncontro(string $id)
    {
        // Validação
        $validator = Validator::make(['idCelula' => $id], [
            'idCelula' => 'required|integer|exists:celulas,id',  // Verifica se a célula existe
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $hoje = date('Y-m-d');
            $proximoEncontro = EncontrosCelulas::where('idCelula', $id)
                ->with('localizacao', 'responsavel')
                ->whereNull('qtdePresentes')
                ->where('dataEncontro', '>=', $hoje)
                ->orderBy('dataEncontro', 'asc')
                ->first();
        } catch (Exception $e) {
            return response()->json(['erro' => $e->getMessage()]);
        }

        return $proximoEncontro;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validação
        $validator = Validator::make($request->all(), [
            'idCelula' => 'required|integer',
            'idLocal' => 'required|integer',
            'idPessoaEstudo' => 'required|integer',
            'dataEncontro' => 'required|date', 
            'qtdePresentes' => 'nullable|integer',
            'temaEstudo' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Lógica de atualização do recurso
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Validação
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:encontros_celulas,id',  // Verifica se o encontro existe
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Lógica para remover o recurso
    }
}
