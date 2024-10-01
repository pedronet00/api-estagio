<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoPost;
use Exception;
use Validator;

class TipoPostController extends Controller
{
    
    public function index()
    {
        return TipoPost::all();
    }

    public function store(Request $request)
    {
        try{
            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'tipoPost' => 'required|unique:tipo_posts,tipoPost'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'O tipo de post já existe ou é inválido.'], 422);
            }

            $tipoPost = TipoPost::create([
                'tipoPost' => $request->tipoPost
            ]);

        } catch(Exception $e){
            return response()->json(['message' => 'Não foi possível salvar o tipo de post.', 'error' => $e->getMessage()], 500);
        }

        return response()->json(['tipoPost' => $tipoPost], 201);
    }

    public function show(string $id)
    {
        try{

            $tipoPost = TipoPost::find($id);

            if(!$tipoPost){
                throw new Exception("Tipo de post não encontrado!");
            }

        } catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 404);
        }

        return response()->json(['tipoPost' => $tipoPost], 200);
    }

    public function update(Request $request, string $id)
    {
        try{

            $tipoPost = TipoPost::find($id);

            $tipoPost->tipoPost = $request->tipoPost ?? $tipoPost->tipoPost;
            $tipoPost->save();

        } catch(Exception $e){
            return response()->json(['error'=>$e->getMessage()], 404);
        }

        return response()->json(['message'=> 'Atualizado com sucesso!', 'tipoPost' => $tipoPost], 200);
    }

    
    public function destroy(string $id)
    {
        try{

            $tipoPost = TipoPost::find($id);

            if(!$tipoPost){
                throw new Exception("Tipo de post não encontrado!");  
            }

            $tipoPost->delete();

        } catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 404); 
        }

        return response()->json(['message'=> 'Tipo de post excluído com sucesso!'], 200);
    }
}
