<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{
    
    public function index()
{
    return User::with('nivelUsuario')->orderBy('name', 'asc')->get();
}


    public function store(Request $request)
    {
        try {
            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'nivelUsuario' => 'required',
                'dataNascimentoUsuario' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'nivelUsuario' => $request->nivelUsuario,
                'imgUsuario' => $request->imgUsuario,
                'dataNascimentoUsuario' => $request->dataNascimentoUsuario,
                'usuarioAtivo' => true,
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao criar usuário: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Usuário criado com sucesso!', 'user' => $user], 201);
    }

    public function show(string $id)
    {
        try{
            $user = User::find($id);

            if(!$user){
                throw new Exception("Usuário não encontrado!");
            }

        } catch(Exception $e){
            return response()->json(['error' => 'Erro ao buscar usuário: '. $e->getMessage()], 404);
        }

        return response()->json(['user' => $user], 200);
    }

    public function deactivate(string $id)
    {
        
        try{

            $user = User::find($id);

            if(!$user){
                return response()->json(['error' => 'Usuário não encontrado!'], 404);
            }

            $user->usuarioAtivo = false;
            $user->save();

        } catch(Exception $e){
            return response()->json(['error' => 'Erro ao desativar usuário: '. $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Usuário desativado com sucesso!'], 200);
    }

    public function activate(string $id)
    {

        try{

            if(!$id){
                throw new Exception("ID não informado!");
            }

            $user = User::find($id);

            if(!$user){
                throw new Exception("Usuário não encontrado");
            }

            if($user->usuarioAtivo == true){
                throw new Exception("Usuário já está ativo!");
            }

            $user->usuarioAtivo = true;
            $user->save();


        } catch(Exception $e){
            return response()->json(['error' => 'Erro ao ativar usuário: '. $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Usuário ativado com sucesso!'], 200);
        
    }
    
    public function update(Request $request, string $id)
    {
        try{

            $user = User::find($id);

            if(!$user){
                throw new Exception("Usuário não encontrado!");
            }

            

            $user->name = $request->name?? $user->name;
            $user->email = $request->email?? $user->email;
            $user->password = $request->password?? $user->password;
            $user->nivelUsuario = $request->nivelUsuario?? $user->nivelUsuario;
            $user->imgUsuario = $request->imgUsuario?? $user->imgUsuario;
            $user->dataNascimentoUsuario = $request->dataNascimentoUsuario?? $user->dataNascimentoUsuario;
            $user->save();

        } catch(Exception $e){
            return response()->json(['error' => 'Erro ao editar usuário: '. $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Usuário editado com sucesso!', 'user' => $user], 200);
    }

    public function destroy(string $id)
    {
        try{
            $user = User::find($id);

            if(!$user){
                throw new Exception("Usuário não encontrado!");
            }

            $user->delete();
        } catch(Exception $e){
            return response()->json(['error' => 'Erro ao deletar usuário: '. $e->getMessage()], 404);
        }
    }

    public function gerarRelatorioUsuarios()
    {
        try{

            $data_hoje = date("Y-m-d H:i");

            $usuarios = User::where('nivelUsuario', '<=', 3)->get();

            if(!$usuarios){
                throw new Exception("Nenhum usuário encontrado!");
            }

        } catch(Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }

        return response()->json(['message' => 'Relatório gerado com sucesso!', 'titulo' => 'Relatório dos Membros da Primeira Igreja Batista de Presidente Prudente', 'usuarios' => $usuarios, 'data' => $data_hoje], 200);
    }

    public function gerarRelatorioPastores()
    {
        try{

            $data_hoje = date("Y-m-d H:i");

            $pastores = User::where('nivelUsuario', '>', 3)->get();

            if($pastores->isEmpty()){
                throw new Exception("Nenhum pastor encontrado!");
            }

        } catch(Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Relatório gerado com sucesso!', 'titulo' => 'Relatório dos Pastores da Primeira Igreja Batista de Presidente Prudente', 'pastores' => $pastores, 'data' => $data_hoje], 200);
    }
}
