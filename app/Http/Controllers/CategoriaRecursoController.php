<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CategoriaRecurso;
use Exception;

class CategoriaRecursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return CategoriaRecurso::where('idCliente', $request->idCliente)
            ->orderby('categoriaRecurso', 'asc')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categoriaRecurso' => 'required|string|max:255',
            'idCliente' => 'required|integer|exists:clientes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Erro de validação', 'errors' => $validator->errors()], 422);
        }

        try {
            $categoriaRecurso = CategoriaRecurso::create($validator->validated());
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao salvar categoria de recurso: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Sucesso!', 'categoriaRecurso' => $categoriaRecurso], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'categoriaRecurso' => 'required|string|max:255',
            'idCliente' => 'required|integer|exists:clientes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Erro de validação', 'errors' => $validator->errors()], 422);
        }

        try {
            $categoriaRecurso = CategoriaRecurso::findOrFail($id);
            $categoriaRecurso->update($validator->validated());
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar categoria de recurso: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Atualizado com sucesso!', 'categoriaRecurso' => $categoriaRecurso], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
