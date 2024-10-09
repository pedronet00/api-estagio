<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriaRecurso;
use Exception;

class CategoriaRecursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return CategoriaRecurso::where('idCliente', $request->idCliente)->get();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{

            if(!$request->categoriaRecurso){
                throw new Exception("Você deve preencher a categoria do recurso!");
            }

            $categoriaRecurso = CategoriaRecurso::create([
                'categoriaRecurso' => $request->categoriaRecurso,
                'idCliente' => $request->idCliente
            ]);

        } catch(Exception $e){
            return response()->json(['error' => 'Erro ao salvar categoria de recurso: '. $e->getMessage()], 404);
        }

        return response()->json(['message' => 'Sucesso!', 'categoriaRecurso' => $categoriaRecurso], 201);
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
