<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MembrosCelulas;

class MembrosCelulasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validando os parâmetros de entrada
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente é obrigatório, inteiro e existente
            'idCelula' => 'required|integer|exists:celulas,id', // idCelula é obrigatório, inteiro e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        return MembrosCelulas::where('idCliente', $request->idCliente)
            ->where('idCelula', $request->idCelula)
            ->with('celula', 'pessoa')
            ->get();
    }

    public function store(Request $request)
    {
        // Validando os dados de entrada
        $validator = Validator::make($request->all(), [
            'idCelula' => 'required|integer|exists:celulas,id', // idCelula obrigatório e existente
            'idPessoa' => 'required|integer|exists:pessoas,id', // idPessoa obrigatório e existente
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $membroCelula = MembrosCelulas::create([
                'idCelula' => $request->idCelula,
                'idPessoa' => $request->idPessoa,
                'idCliente' => $request->idCliente,
                'status' => 1
            ]);
        } catch (Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }

        return response()->json(['sucesso' => $membroCelula]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Aqui pode ser necessário adicionar validação, caso esse método seja implementado posteriormente
        return MembrosCelulas::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Similar ao show, o edit precisa de validação caso seja implementado
        return MembrosCelulas::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validando os dados de entrada para update
        $validator = Validator::make($request->all(), [
            'idCelula' => 'nullable|integer|exists:celulas,id', // idCelula opcional e existente
            'idPessoa' => 'nullable|integer|exists:pessoas,id', // idPessoa opcional e existente
            'idCliente' => 'nullable|integer|exists:clientes,id', // idCliente opcional e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $membroCelula = MembrosCelulas::findOrFail($id);

        $membroCelula->idCelula = $request->has('idCelula') ? $request->idCelula : $membroCelula->idCelula;
        $membroCelula->idPessoa = $request->has('idPessoa') ? $request->idPessoa : $membroCelula->idPessoa;
        $membroCelula->idCliente = $request->has('idCliente') ? $request->idCliente : $membroCelula->idCliente;
        $membroCelula->save();

        return response()->json(['sucesso' => $membroCelula]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $membroCelula = MembrosCelulas::find($id);

            if (!$membroCelula) {
                throw new Exception("Membro não existe na célula.");
            }

            $membroCelula->delete();
        } catch (Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Membro excluído com sucesso!'], 200);
    }
}
