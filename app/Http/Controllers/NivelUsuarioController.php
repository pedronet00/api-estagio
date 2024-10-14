<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NivelUsuario;
use Exception;

class NivelUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return NivelUsuario::all();
    }


    public function show(string $id)
    {
        try{
            $nivelUsuario = NivelUsuario::find($id);

            if(!$nivelUsuario){
                throw new Exception("Nível de usuário não encontrado!");
            }

        } catch(Exception $e){

            return response()->json(['error' => $e->getMessage()], 404);
        }

        return response()->json(['nivelUser' => $nivelUsuario], 200);
    }

    public function update(Request $request, string $id)
    {
        try{

            $nivelUsuario = NivelUsuario::find($id);

            $nivelUsuario->nivelUsuario = $request->nivelUsuario ?? $nivelUsuario->nivelUsuario;
            $nivelUsuario->save();

        } catch(Exception $e){
            return response()->json(['error'=>$e->getMessage()], 404);
        }

        return response()->json(['message'=> 'Atualizado com sucesso!', 'nivelUsuario' => $nivelUsuario], 200);
    }

    public function destroy(string $id)
    {
        //
    }
}
