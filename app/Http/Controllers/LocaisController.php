<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Locais;
use Exception;

class LocaisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Locais::where('idCliente', $request->idCliente)->get();
    }


    public function store(Request $request)
    {
        try{

            if(!$request->nomeLocal){
                throw new Exception("Você deve preencher o nome do local!");
            }

            $local = Locais::create([
                'nomeLocal' => $request->nomeLocal,
                'statusLocal' => 1,
                'idCliente' => $request->idCliente
            ]);

        } catch(Exception $e){
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



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
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
