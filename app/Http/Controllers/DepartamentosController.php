<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamentos;
use Illuminate\Support\Facades\DB;
use App\Models\Clientes;
use App\Models\Planos;
use Illuminate\Support\Facades\Validator;
use Exception;

class DepartamentosController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $idCliente = $request->query('idCliente');
        return Departamentos::where('idCliente', $idCliente)->get();
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'tituloDepartamento' => 'required|string|max:255',
        'textoDepartamento' => 'required|string',
        'imgDepartamento' => 'sometimes|file|image|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        $departamento = new Departamentos();
        $departamento->tituloDepartamento = $request->tituloDepartamento;
        $departamento->textoDepartamento = $request->textoDepartamento;
        $departamento->statusDepartamento = 1;

        if ($request->hasFile('imgDepartamento')) {
            $file = $request->file('imgDepartamento');
            if ($file->isValid()) {
                $departamento->imgDepartamento = $file->store('departamentos', 'public');
            } else {
                throw new Exception("Arquivo de imagem inválido.");
            }
        }

        $departamento->idCliente = $request->idCliente;
        $departamento->save();

        return response()->json(['message' => 'Departamento criado com sucesso!', 'departamento' => $departamento], 201);
    } catch (Exception $e) {
        return response()->json(['message' => 'Erro ao criar departamento', 'error' => $e->getMessage()], 500);
    }
}



    public function show(Request $request)
    {
        $validator = Validator::make(['id' => $request->id], [
            'id' => 'required|integer|exists:departamentos,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $departamento = Departamentos::find($request->id);
        if (!$departamento) {
            return response()->json(['error' => 'Departamento não encontrado!'], 404);
        }

        if($departamento->idCliente != $request->idCliente){
            return response()->json(['error' => 'Esse departamento não é seu!'], 403);
        }

        return response()->json(['departamento' => $departamento], 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tituloDepartamento' => 'string|max:255',
            'textoDepartamento' => 'string',
            'imgDepartamento' => 'sometimes|file|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $departamento = Departamentos::find($request->id);
            if (!$departamento) {
                throw new Exception("Departamento não encontrado!");
            }

            // if ($request->hasFile('imgDepartamento') && $request->file('imgDepartamento')->isValid()) {
            //     $departamento->imgDepartamento = $request->file('imgDepartamento')->store('departamentos', 'public');
            // }

            $departamento->tituloDepartamento = $request->tituloDepartamento ?? $departamento->tituloDepartamento;
            $departamento->textoDepartamento = $request->textoDepartamento ?? $departamento->textoDepartamento;
            $departamento->save();

            return response()->json(['message' => 'Departamento atualizado com sucesso!', 'departamento' => $departamento], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar departamento', 'error' => $e->getMessage()], 500);
        }
    }

    public function deactivate($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:departamentos,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $departamento = Departamentos::find($id);
            if (!$departamento || $departamento->statusDepartamento == false) {
                throw new Exception("Departamento não encontrado ou já desativado!");
            }

            $departamento->statusDepartamento = false;
            $departamento->save();

            return response()->json(['message' => 'Departamento desativado com sucesso!'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function activate($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:departamentos,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $departamento = Departamentos::find($id);
            if (!$departamento || $departamento->statusDepartamento == true) {
                throw new Exception("Departamento não encontrado ou já ativo!");
            }

            $departamento->statusDepartamento = true;
            $departamento->save();

            return response()->json(['message' => 'Departamento ativado com sucesso!'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function gerarRelatorioDepartamentos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id',
            'dataInicial' => 'required|date',
            'dataFinal' => 'required|date|after_or_equal:dataInicial',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $dataInicial = $request->dataInicial;
            $dataFinal = date('Y-m-d H:i:s', strtotime($request->dataFinal . ' 23:59:59'));
            $idCliente = $request->idCliente;

            $departamentoCount = Departamentos::where('idCliente', $idCliente)
                ->whereBetween('created_at', [$dataInicial, $dataFinal])
                ->count();

            $departamentosAtivos = Departamentos::where('idCliente', $idCliente)
                ->where('statusDepartamento', 1)
                ->whereBetween('created_at', [$dataInicial, $dataFinal])
                ->count();

            $departamentosInativos = Departamentos::where('idCliente', $idCliente)
                ->where('statusDepartamento', 0)
                ->whereBetween('created_at', [$dataInicial, $dataFinal])
                ->count();

            $departamentos = Departamentos::where('idCliente', $idCliente)
                ->whereBetween('created_at', [$dataInicial, $dataFinal])
                ->orderBy('tituloDepartamento', 'asc')
                ->get();

            if ($departamentos->isEmpty()) {
                throw new Exception("Nenhum departamento encontrado!");
            }

            return response()->json([
                'message' => 'Relatório gerado com sucesso!',
                'titulo' => 'Relatório dos Departamentos da Primeira Igreja Batista de Presidente Prudente',
                'qtdeDepartamentos' => $departamentoCount,
                'qtdeDepartamentosAtivos' => $departamentosAtivos,
                'qtdeDepartamentosInativos' => $departamentosInativos,
                'departamentos' => $departamentos,
                'data' => now(),
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    public function destroy(string $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:departamentos,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $departamento = Departamentos::find($id);
            if (!$departamento) {
                throw new Exception("Departamento não encontrado!");
            }

            $departamento->delete();
            return response()->json(['message' => 'Departamento excluído com sucesso!'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao excluir departamento', 'error' => $e->getMessage()], 500);
        }
    }
}
