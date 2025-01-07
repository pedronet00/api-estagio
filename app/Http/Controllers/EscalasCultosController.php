<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EscalasCultos;
use Exception;

class EscalasCultosController extends Controller
{
    public function index(Request $request)
    {
        return EscalasCultos::with(['culto', 'funcaoCulto', 'pessoa'])->where('idCulto', $request->idCulto)->get();
    }

    public function store(Request $request)
    {
        try{

            $validated = $request->validate([
                'idCulto' => 'required|exists:cultos,id',
                'idFuncaoCulto' => 'required|exists:funcoes_cultos,id',
                'idPessoa' => 'required|exists:users,id',
                'idCliente' => 'required|integer',
            ]);
    
            $usuario_ja_ocupa_funcao_no_culto = EscalasCultos::where('idCulto', $request->idCulto)
            ->where('idPessoa', $request->idPessoa)
            ->exists();
    
            if($usuario_ja_ocupa_funcao_no_culto){
                return response()->json(['error' => 'Usuário já está escalado para uma função nesse culto.'], 422);
            }

            EscalasCultos::create($validated);
    

        } catch(Exception $e){
            return response()->json(['error' => 'Erro: '. $e->getMessage()], 500);

        }
        return  response()->json(['sucesso' => 'Escala criada com sucesso!'], 200);

    }

    public function show(Request $request)
    {
        return EscalasCultos::with(['culto', 'funcaoCulto', 'pessoa'])->where('id', $request->idEscala)->get();
    }

    public function mostrarEscalasUsuario(Request $request)
    {
        try{

            $escalas_usuarios = EscalasCultos::where('idPessoa', $request->idPessoa)->with('culto', 'funcaoCulto', 'pessoa')->get();

            if(!$escalas_usuarios){
                return response()->json(['error' => 'Erro: Esse usuário não está escalado para nenhuma função.'], 422);
            }

        } catch(Exception $e){
            return response()->json(['error' => 'Erro: '. $e->getMessage()], 500);
        }

        return $escalas_usuarios;
    }

    public function update(Request $request)
{
    try {
        // Validação dos dados recebidos
        $validated = $request->validate([
            'idCulto' => 'required|exists:cultos,id',
            'idFuncaoCulto' => 'required|exists:funcoes_cultos,id',
            'idPessoa' => 'required|exists:users,id',
        ]);

        // Encontra a escala específica do culto. Aqui, usamos `find()` ou `findOrFail()`
        $escala = EscalasCultos::where('idCulto', $request->idCulto)->first();  // Usa `first()` para pegar o primeiro registro ou null

        // Verifica se a escala foi encontrada
        if (!$escala) {
            return response()->json(['error' => 'Culto não encontrado.'], 404);
        }

        // Verifica se o usuário já está escalado para a função no culto
        $usuario_ja_ocupa_funcao_no_culto = EscalasCultos::where('idCulto', $request->idCulto)
            ->where('idPessoa', $request->idPessoa)
            ->exists();

        if ($usuario_ja_ocupa_funcao_no_culto) {
            return response()->json(['error' => 'Usuário já está escalado para uma função nesse culto.'], 422);
        }

        // Atualiza os dados da escala
        $escala->update($validated);  // Atualiza diretamente a instância

    } catch (Exception $e) {
        // Em caso de erro, retorna a mensagem de erro
        return response()->json(['error' => $e->getMessage()], 500);
    }

    return response()->json($escala);  // Retorna a escala atualizada
}


    public function destroy(Request $request)
    {
        $escala = EscalasCultos::findOrFail($request->id);
        $escala->delete();

        return response()->json(['message' => 'Escala deletada com sucesso']);
    }
    
}
