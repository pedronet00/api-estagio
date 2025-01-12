<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Entradas;
use Illuminate\Support\Facades\Validator;

class EntradasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validação para garantir que o idCliente esteja presente e seja um inteiro
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $entradas = Entradas::where('idCliente', $request->idCliente)
            ->with('cliente')
            ->orderBy('data', 'asc')
            ->get();
        
        return response()->json($entradas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validação para garantir que os dados necessários estejam presentes
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'categoria' => 'required|integer',
            'data' => 'required|date',
            'idCliente' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Criação da entrada
        $entrada = Entradas::create($request->all());

        return response()->json($entrada, 201);
    }

    public function delete($id)
    {
        try{

            $saida = Entradas::find($id);
            $saida->delete();

        } catch(Exception $e){
            return response()->json(['erro' => $e->getMessage()]);
        }

        return response()->json(['sucesso' => "Saída excluída com sucesso."]);
    }
}
