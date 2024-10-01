<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departamentos;
use Exception;

class DepartamentosController extends Controller
{
    
    public function index()
    {
        return Departamentos::all();
    }

    public function store(Request $request)
    {
        try{

            if(!$request->tituloDepartamento){
                throw new Exception("Informe o título do departamento!");
            }

            if(!$request->textoDepartamento){
                throw new Exception("Informe o texto do departamento!");
            }

            $departamento = Departamentos::create([
                'tituloDepartamento' => $request->tituloDepartamento,
                'textoDepartamento' => $request->textoDepartamento,
                'imgDepartamento' => $request->imgDepartamento,
                'statusDepartamento' => 1
            ]);

        } catch(Exception $e){
            return response()->json(['message'=>'Erro ao cadastrar departamento:', 'error' => $e->getMessage()], 500);
        }

        return response()->json(['message'=> 'Departamento cadastrado com sucesso!', 'departamento' => $departamento], 201);
    }

    public function show(string $id)
    {
        try{

            if(!$id){
                throw new Exception("ID do departamento não fornecido!");
            }

            $departamento = Departamentos::find($id);

            if(!$departamento){
                throw new Exception("Departamento não encontrado!");
            }

        } catch(Exception $e){
            return response()->json(['error' => 'Erro ao buscar departamento: '. $e->getMessage()], 404);
        }

        return response()->json(['departamento' => $departamento], 200);
    }

    public function update(Request $request, string $id)
    {
        try{

        $departamento = Departamentos::find($id);

        if(!$departamento){
            throw new Exception("Departamento não encontrado!");
        }

        $departamento->tituloDepartamento = $request->tituloDepartamento ?? $departamento->tituloDepartamento;
        $departamento->textoDepartamento = $request->textoDepartamento?? $departamento->textoDepartamento;
        $departamento->imgDepartamento = $request->imgDepartamento?? $departamento->imgDepartamento;
        $departamento->save();

        } catch(Exception $e){
            return response()->json(['message'=> 'Erro ao atualizar departamento', 'error' => $e->getMessage]);
        }

        return response()->json(['message'=> 'Departamento atualizado com sucesso!', 'departamento' => $departamento], 200);
    }

    public function deactivate($id)
    {

        try{
            if(!$id){
                throw new Exception("ID do departamento não fornecido!");
            }

            $departamento = Departamentos::find($id);

            if(!$departamento){
                throw new Exception("Departamento não encontrado!");
            }

            if($departamento->statusDepartamento == false){
                throw new Exception("Departamento já está desativado!");
            }

            $departamento->statusDepartamento = false;
            $departamento->save();

        } catch(Exception $e){
            return response()->json(['message' => $e->getMessage]);
        }

        return response()->json(['message'=> 'Departamento desativado com sucesso!']);


    }

    public function activate($id)
    {
        try{

            if(!$id){
                throw new Exception("ID do departamento não fornecido!");
            }

            $departamento = Departamentos::find($id);

            if(!$departamento){
                throw new Exception("Departamento não encontrado!");
            }

            if($departamento->statusDepartamento == true){
                throw new Exception("Departamento já está ativo!");
            }

            $departamento->statusDepartamento = true;
            $departamento->save();

        } catch(Exception $e){
            return response()->json(['message' => $e->getMessage]);
        }

        return response()->json(['message'=> 'Departamento desativado com sucesso!']);
    }

    public function search(Request $rquest){
        
    }

    public function destroy(string $id)
    {
        //
    }
}
