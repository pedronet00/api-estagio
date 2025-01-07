<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PerfisFuncoes;
use Illuminate\Support\Facades\Validator;

class PerfisFuncoesController extends Controller
{
    
    public function index(Request $request)
    {
        // Validando o idCliente
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id', // idCliente obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        return PerfisFuncoes::with('perfil', 'funcao')->where('idCliente', $request->idCliente)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validando os dados de entrada
        $validator = Validator::make($request->all(), [
            'idPerfil' => 'required|integer|exists:perfis,id', // idPerfil obrigatório e existente
            'idFuncao' => 'required|integer|exists:funcoes,id', // idFuncao obrigatório e existente
            'permissao' => 'required|boolean', // permissao obrigatório e do tipo boolean
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $regraPerfil = PerfisFuncoes::create([
                'idPerfil' => $request->idPerfil,
                'idFuncao' => $request->idFuncao,
                'permissao' => $request->permissao,
                'idCliente' => $request->idCliente
            ]);

        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao salvar!', 'erro' => $e->getMessage()]);
        }

        return response()->json([
            'message' => 'Regra de função para o perfil criada com sucesso!',
            'regraPerfil' => $regraPerfil,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Validando o idPerfil
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:perfis,id', // idPerfil obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        return PerfisFuncoes::with('funcao')->where('idPerfil', $id)->get();
    }

    public function showPerfilFuncaoCliente(string $id)
    {
        // Validando o idCliente
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:clientes,id', // idCliente obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        return PerfisFuncoes::with('funcao')->where('idCliente', $id)->get();
    }

    public function showPerfilFuncao(Request $request)
    {
        // Validando idPerfil e idFuncao
        $validator = Validator::make($request->all(), [
            'idPerfil' => 'required|integer|exists:perfis,id', // idPerfil obrigatório e existente
            'idFuncao' => 'required|integer|exists:funcoes,id', // idFuncao obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        return PerfisFuncoes::with('funcao')
            ->where('idPerfil', $request->idPerfil)
            ->where('idFuncao', $request->idFuncao)
            ->get();
    }

    public function ativarFuncaoPerfil(Request $request)
    {
        // Validando idPerfil e idFuncao
        $validator = Validator::make($request->all(), [
            'idPerfil' => 'required|integer|exists:perfis,id', // idPerfil obrigatório e existente
            'idFuncao' => 'required|integer|exists:funcoes,id', // idFuncao obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $regraPerfil = PerfisFuncoes::where('idPerfil', $request->idPerfil)->where('idFuncao', $request->idFuncao)->first();
        if (!$regraPerfil) {
            return response()->json(['message' => 'Regra de função não encontrada.'], 404);
        }

        $regraPerfil->permissao = 1;
        $regraPerfil->save();

        return response()->json(['message' => 'Função ativada com sucesso!'], 200);
    }

    public function desativarFuncaoPerfil(Request $request)
    {
        // Validando idPerfil e idFuncao
        $validator = Validator::make($request->all(), [
            'idPerfil' => 'required|integer|exists:perfis,id', // idPerfil obrigatório e existente
            'idFuncao' => 'required|integer|exists:funcoes,id', // idFuncao obrigatório e existente
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $regraPerfil = PerfisFuncoes::where('idPerfil', $request->idPerfil)->where('idFuncao', $request->idFuncao)->first();
        if (!$regraPerfil) {
            return response()->json(['message' => 'Regra de função não encontrada.'], 404);
        }

        $regraPerfil->permissao = 0;
        $regraPerfil->save();

        return response()->json(['message' => 'Função desativada com sucesso!'], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Adicione aqui validações se necessário
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Adicione a validação e lógica de atualização aqui, conforme necessário
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Adicione a lógica de exclusão com validação aqui, se necessário
    }
}
