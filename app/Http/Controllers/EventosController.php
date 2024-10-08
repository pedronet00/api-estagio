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

    
    public function update(Request $request, string $id)
    {
        try{

            $evento = Eventos::find($id);

            if(!$evento){
                throw new Exception("Evento não encontrado!");
            }

            $evento->nomeEvento = $request->nomeEvento ?? $evento->nomeEvento;
            $evento->descricaoEvento = $request->descricaoEvento ?? $evento->descricaoEvento;
            $evento->localEvento = $request->localEvento ?? $evento->localEvento;
            $evento->dataEvento = $request->dataEvento ?? $evento->dataEvento;
            $evento->prioridadeEvento = $request->prioridadeEvento ?? $evento->prioridadeEvento;
            $evento->orcamentoEvento = $request->orcamentoEvento ?? $evento->orcamentoEvento;

            $evento->save();

        } catch(Exception $e){
            return response()->json(['message' => "Erro ao editar evento!", 'error' => $e->getMessage()]);
        }

        return response()->json(['message' => "Evento editado com sucesso!", 'evento' => $evento]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
