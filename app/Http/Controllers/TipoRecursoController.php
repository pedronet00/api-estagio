<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoRecurso;
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
        try{

            if(!$request->tipoRecurso){
                throw new Exception("Você deve preencher o tipo de recurso!");
            }

            $tipoRecurso = TipoRecurso::create([
                'tipoRecurso' => $request->tipoRecurso,
                'idCliente' => $request->idCliente
            ]);

        } catch(Exception $e){
            return response()->json(['error' => 'Erro ao salvar tipo de recurso: '. $e->getMessage()], 404);
        }

        return response()->json(['message' => 'Sucesso!', 'tipoRecurso' => $tipoRecurso], 201);
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
