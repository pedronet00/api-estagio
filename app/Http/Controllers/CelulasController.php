<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Celulas;
use Illuminate\Support\Facades\Validator;

class CelulasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $celulas =  Celulas::with('localizacao', 'responsavel')->where('idCliente', $request->idCliente)->get();

        return $celulas;
    }

    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'nomeCelula' => 'required|string|max:255',
                'localizacaoCelula' => 'required|integer',
                'responsavelCelula' => 'required|integer',
                'diaReuniao' => 'required|integer', 
                'idCliente' => 'required|integer',
                'imagemCelula' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
    
            $imagePath = null;
            if ($request->hasFile('imagemCelula')) {
                $imagePath = $request->file('imagemCelula')->store('uploads', 'public');
            }
        
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }
    
            $celula = Celulas::create([
                'nomeCelula' => $request->nomeCelula,
                'responsavelCelula' => $request->responsavelCelula,
                'localizacaoCelula' => $request->localizacaoCelula,
                'diaReuniao' => $request->diaReuniao,
                'imagemCelula' => $imagePath,
                'idCliente' => $request->idCliente
            ]);

        } catch(Exception $e){
            return response()->json(['message' => 'Erro ao salvar!', 'erro' => $e->getMessage()]);
        }
    
        return response()->json([
            'message' => 'Célula criada com sucesso!',
            'celula' => $celula,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $celula = Celulas::find($id);

        if(!$celula)
        {
            return response()->json(['message' => 'Erro! Não foi possível encontrar essa célula.'], 500);
        }

        return $celula;
    }


    public function update(Request $request, string $id)
    {
        
        try{

            $celula = Celulas::find($id);

            if(!$celula){
                return response()->json(['message' => 'Erro! Não foi possível encontrar essa célula.'], 500);
            }

            $celula->nomeCelula = $request->nomeCelula ?? $celula->nomeCelula;
            $celula->localizacaoCelula = $request->localizacaoCelula ?? $celula->localizacaoCelula;
            $celula->responsavelCelula = $request->responsavelCelula ?? $celula->responsavelCelula;
            $celula->diaReuniao = $request->diaReuniao ?? $celula->diaReuniao;
            $celula->imagemCelula = $request->imagemCelula ?? $celula->imagemCelula;
            $celula->save();

        } catch(Exception $e){
            return response()->json(['error' => 'Erro ao editar célula: '. $e->getMessage()], 500);
        }

        return response()->json(['sucesso' => $celula], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $celula = Celulas::find($id);

            if(!$celula){
                throw new Exception("Célula não encontrada!");
            }

            $celula->delete();

        } catch(Exception $e){
            return response()->json(['message' => 'Erro ao deletar: '. $e->getMessage()]);
        }

        return response()->json(['message' => 'Célula deletada com sucesso!'], 200);
    }

   
}
