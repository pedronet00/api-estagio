<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Locais;
use Exception;

class LocaisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validando o parâmetro idCliente
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente é obrigatório, inteiro e existente na tabela clientes
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        return Locais::where('idCliente', $request->idCliente)->get();
    }

    public function store(Request $request)
    {
        // Validando os campos de entrada
        $validator = Validator::make($request->all(), [
            'nomeLocal' => 'required|string|max:255', // nomeLocal é obrigatório, uma string e com tamanho máximo de 255 caracteres
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente é obrigatório, inteiro e existente na tabela clientes
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $local = Locais::create([
                'nomeLocal' => $request->nomeLocal,
                'statusLocal' => 1,
                'idCliente' => $request->idCliente
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Sucesso!', 'local' => $local], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $local = Locais::findOrFail($id);

        return $local;
    }

    public function activate(string $id)
    {
        $local = Locais::find($id);
        $local->statusLocal = 1;
        $local->save();

        return response()->json(['message' => 'Local ativado com sucesso!']);
    }

    public function deactivate(string $id)
    {
        $local = Locais::find($id);
        $local->statusLocal = 0;
        $local->save();

        return response()->json(['message' => 'Local desativado com sucesso!']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validando os campos de entrada
        $validator = Validator::make($request->all(), [
            'nomeLocal' => 'nullable|string|max:255', // nomeLocal é opcional, uma string e com tamanho máximo de 255 caracteres
            'statusLocal' => 'nullable|boolean', // statusLocal é opcional e deve ser um valor booleano
            'idCliente' => 'nullable|integer|exists:clientes,id', // idCliente é opcional, inteiro e existente na tabela clientes
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $local = Locais::findOrFail($id);

        $local->nomeLocal = $request->has('nomeLocal') ? $request->nomeLocal : $local->nomeLocal;
        $local->statusLocal = $request->has('statusLocal') ? $request->statusLocal : $local->statusLocal;
        $local->save();

        return response()->json([
            'message' => 'Sucesso!',
            'local' => $local
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $local = Locais::findOrFail($id);
        $local->delete();

        return response()->json([
            'message' => 'Local deletado com sucesso!'
        ]);
    }
}
