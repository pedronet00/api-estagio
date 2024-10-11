<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassesEBD;
use Exception;

class ClassesEBDController extends Controller
{
    
    public function index(Request $request)
    {
        return ClassesEBD::where('idCliente', $request->idCliente)->get();
    }


    public function store(Request $request)
    {
        try{

            if(!$request->nomeClasse){
                throw new Exception("O nome da classe é obrigatório!");
            }

            if(!$request->quantidadeMembros){
                throw new Exception("A quantidade de membros da classe é obrigatória!");
            }

            if(!$request->idCliente){
                throw new Exception("Sem ID de cliente");
            }

            $classe = ClassesEBD::create([
                'nomeClasse' => $request->nomeClasse,
                'quantidadeMembros' => $request->quantidadeMembros,
                'statusClasse' => 1,
                'idCliente' => $request->idCliente
            ]);

        } catch(Exception $e){
            return response()->json(['message' => 'Erro ao salvar classe!', 'erro' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Sucesso!', 'classe' => $classe], 200);
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
