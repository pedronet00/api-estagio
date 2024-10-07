<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Eventos;

class EventosController extends Controller
{
    
    public function index()
    {
        return Eventos::with(['local'])->get();
    }

    
    public function store(Request $request)
    {
        try{

            $evento = Eventos::create([
                "nomeEvento" => $request->nomeEvento,
                "descricaoEvento" => $request->descricaoEvento,
                "dataEvento" => $request->dataEvento,
                "localEvento" => $request->localEvento,
                "prioridadeEvento" => $request->prioridadeEvento,
                "orcamentoEvento" => $request->orcamentoEvento
            ]);

        } catch(Exception $e){
            return response()->json(['error' => 'Ocorreu um erro ao salvar o evento.'], 500);
        }

        return response()->json(['message' => 'O evento foi salvo com sucesso!', 'evento' => $evento], 201);
    }

    
    public function show(string $id)
    {
        try{

            if(!$request->id){
                return response()->json(['error' => 'ID do evento não informado.'], 400);
            }

            $evento = Eventos::find($id);

            if(!$evento){
                return response()->json(['error' => 'Evento não encontrado.'], 404);
            }

        } catch(Exception $e){
            return response()->json(['error' => 'Ocorreu um erro ao buscar o evento.'], 500);
        }

        return response()->json(['evento' => $evento], 200);
    }

    
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
