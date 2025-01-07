<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Perfis;
use Illuminate\Support\Facades\Validator;

class PerfisController extends Controller
{
    
    public function index(Request $request)
    {
        // Validando a entrada
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente é obrigatório e deve existir
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        return Perfis::where('idCliente', 0)->orWhere('idCliente', $request->idCliente)->get();
    }

    public function store(Request $request)
    {
        // Validando os dados de entrada
        $validator = Validator::make($request->all(), [
            'nomePerfil' => 'required|string|max:255',
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente obrigatório e deve existir
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $perfil = Perfis::create([
                'nomePerfil' => $request->nomePerfil,
                'idCliente' => $request->idCliente,
            ]);

        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao salvar!', 'erro' => $e->getMessage()]);
        }

        return response()->json([
            'message' => 'Perfil criado com sucesso!',
            'perfil' => $perfil,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Validando o ID
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:perfis,id', // ID obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        return Perfis::where('id', $id)->get();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Adicione aqui validações se necessário
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validando os dados de entrada para atualização
        $validator = Validator::make($request->all(), [
            'nomePerfil' => 'nullable|string|max:255',
            'idCliente' => 'nullable|integer|exists:clientes,id', // idCliente deve existir se for passado
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $perfil = Perfis::find($id);

            if (!$perfil) {
                throw new Exception("Perfil não encontrado!");
            }

            $perfil->nomePerfil = $request->nomePerfil ?? $perfil->nomePerfil;
            $perfil->idCliente = $request->idCliente ?? $perfil->idCliente;
            $perfil->save();

        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar!', 'erro' => $e->getMessage()]);
        }

        return response()->json(['message' => 'Perfil atualizado com sucesso!', 'perfil' => $perfil], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Validando o ID
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:perfis,id', // ID obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $perfil = Perfis::find($id);

            if (!$perfil) {
                throw new Exception("Perfil não encontrado!");
            }

            $perfil->delete();

        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao excluir!', 'erro' => $e->getMessage()]);
        }

        return response()->json(['message' => 'Perfil excluído com sucesso!'], 200);
    }
}
