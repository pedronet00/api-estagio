<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Funcoes;
use Illuminate\Support\Facades\Validator;

class FuncoesController extends Controller
{
    public function index(Request $request)
    {
        return Funcoes::where(function ($query) use ($request) {
            $query->where('idCliente', $request->idCliente)
                  ->orWhere('idCliente', 0);
        })
        ->get();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{

            $validator = Validator::make($request->all(), [
                'nome' => 'required|string|max:255',
                'descricao' => 'required|string|max:255',
            ]);

            $funcao = Funcoes::create([
                'nome' => $request->nome,
                'descricao' => $request->descricao,
                'idCliente' => $request->idCliente
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }

        } catch(Exception $e){
            return response()->json(['message' => 'Erro ao salvar!', 'erro' => $e->getMessage()]);
        }
    
        return response()->json([
            'message' => 'Função criada com sucesso!',
            'funcao' => $funcao,
        ], 201);

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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
