<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Saidas;
use Illuminate\Support\Facades\Validator;

class SaidasController extends Controller
{
    public function index(Request $request)
    {
        // Validando o idCliente
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $saidas = Saidas::where('idCliente', $request->idCliente)->with('cliente')->orderBy('data', 'asc')->get();
        return response()->json($saidas);
    }

    public function store(Request $request)
    {
        // Validando os dados de entrada para a criação de uma saída
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente obrigatório e existente
            'descricao' => 'required|string|max:255', // descrição obrigatória
            'quantidade' => 'required|integer|min:1', // quantidade obrigatória e maior ou igual a 1
            'data' => 'required|date', // data obrigatória e no formato de data válido
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Criando a saída no banco de dados
        $saida = Saidas::create($request->all());
        return response()->json($saida, 201);
    }
}
