<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departamentos;
use Exception;

class DepartamentosController extends Controller
{
    
    public function index(Request $request)
    {

        $idCliente = $request->query('idCliente');

        if($idCliente){
            return Departamentos::where('idCliente', $idCliente)->get();
        }
    }

    public function store(Request $request)
{
    try {
        // Validação básica dos campos obrigatórios
        if (!$request->tituloDepartamento) {
            throw new Exception("Informe o título do departamento!");
        }

        if (!$request->textoDepartamento) {
            throw new Exception("Informe o texto do departamento!");
        }

        // Verifica se o arquivo foi enviado
        $imgPath = null;
        if ($request->hasFile('imgDepartamento') && $request->file('imgDepartamento')->isValid()) {
            // Salva a imagem na pasta 'public/departamentos'
            $imgPath = $request->file('imgDepartamento')->store('departamentos', 'public');
        } else {
            throw new Exception("Imagem inválida ou não enviada!");
        }

        // Criação do departamento
        $departamento = Departamentos::create([
            'tituloDepartamento' => $request->tituloDepartamento,
            'textoDepartamento' => $request->textoDepartamento,
            'imgDepartamento' => $imgPath, // Salva o caminho da imagem
            'statusDepartamento' => 1,
            'idCliente' => $request->idCliente
        ]);

    } catch (Exception $e) {
        return response()->json(['message' => 'Erro ao cadastrar departamento:', 'error' => $e->getMessage()], 500);
    }

    return response()->json(['message' => 'Departamento cadastrado com sucesso!', 'departamento' => $departamento], 201);
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

    public function gerarRelatorioDepartamentos(Request $request)
    {
        try{

            $data_hoje = date("Y-m-d H:i");

            $departamentoCount = Departamentos::where('idCliente', $request->idCliente)->count();
            $departamentosAtivos = Departamentos::where('idCliente', $request->idCliente)->where('statusDepartamento', 1)->count();
            $departamentosInativos = Departamentos::where('idCliente', $request->idCliente)->where('statusDepartamento', 0)->count();

            $departamentos = Departamentos::where('idCliente', $request->idCliente)->orderBy('tituloDepartamento', 'asc')->get();

            if(!$departamentos){
                throw new Exception("Nenhum departamento encontrado!");
            }

        } catch(Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }

        return response()->json([
            'message' => 'Relatório gerado com sucesso!', 
            'titulo' => 'Relatório dos Departamentos da Primeira Igreja Batista de Presidente Prudente', 
            'qtdeDepartamentos' => $departamentoCount,
            'qtdeDepartamentosAtivos' => $departamentosAtivos,
            'qtdeDepartamentosInativos' => $departamentosInativos,
            'departamentos' => $departamentos, 
            'data' => $data_hoje
            ],200
        );
    }

    public function search(Request $rquest){
        
    }

    public function destroy(string $id)
    {
        //
    }
}
