<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoRecurso;
use Illuminate\Support\Facades\Validator;
use Exception;

class TipoRecursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return TipoRecurso::where('idCliente', $request->idCliente)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validando os dados de entrada
        $validator = Validator::make($request->all(), [
            'tipoRecurso' => 'required|string|max:255', // tipoRecurso obrigatório e do tipo string
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $tipoRecurso = TipoRecurso::create([
                'tipoRecurso' => $request->tipoRecurso,
                'idCliente' => $request->idCliente
            ]);
        } catch(Exception $e) {
            return response()->json(['error' => 'Erro ao salvar tipo de recurso: '. $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Sucesso!', 'tipoRecurso' => $tipoRecurso], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Implemente a lógica de visualização, se necessário
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Implemente a lógica de edição, se necessário
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implemente a lógica de atualização, se necessário
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Implemente a lógica de remoção, se necessário
    }
}
