<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassesEBD;
use Illuminate\Support\Facades\Validator;
use Exception;

class ClassesEBDController extends Controller
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

        return ClassesEBD::where('idCliente', $request->idCliente)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomeClasse' => 'required|string|max:255',
            'quantidadeMembros' => 'required|integer|min:1',
            'idCliente' => 'required|integer|exists:clientes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $classe = ClassesEBD::create([
                'nomeClasse' => $request->nomeClasse,
                'quantidadeMembros' => $request->quantidadeMembros,
                'statusClasse' => 1,
                'idCliente' => $request->idCliente,
            ]);

            return response()->json(['message' => 'Sucesso!', 'classe' => $classe], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao salvar classe!', 'erro' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:classes_ebd,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $classe = ClassesEBD::find($id);

        if (!$classe) {
            return response()->json(['message' => 'Classe não encontrada.'], 404);
        }

        return $classe;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nomeClasse' => 'sometimes|required|string|max:255',
            'quantidadeMembros' => 'sometimes|required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $classe = ClassesEBD::findOrFail($id);

            $classe->update($validator->validated());

            return response()->json(['message' => 'Classe atualizada com sucesso!', 'classe' => $classe], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar classe!', 'erro' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:classes_ebd,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $classe = ClassesEBD::findOrFail($id);
            $classe->delete();

            return response()->json(['message' => 'Classe deletada com sucesso!'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao deletar classe!', 'erro' => $e->getMessage()], 500);
        }
    }
}
