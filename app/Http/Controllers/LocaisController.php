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
    public function index()
    {
        return Locais::all();
    }


    public function store(Request $request)
    {
        try{

            if(!$request->nomeLocal){
                throw new Exception("Você deve preencher o nome do local!");
            }

            $local = Locais::create([
                'nomeLocal' => $request->nomeLocal,
                'statusLocal' => 1
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
