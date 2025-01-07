<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Clientes;
use App\Models\Planos;
use Exception;
use Validator;

class UserController extends Controller
{
    
    public function index(Request $request)
    {
        // Pega o idCliente da requisição
        $idCliente = $request->idCliente;

        // Verifica se o idCliente foi passado
        if ($idCliente) {
            // Retorna apenas os usuários relacionados ao idCliente
            return User::where('idCliente', $idCliente)
                    ->with('perfil')
                    ->orderBy('name', 'asc')
                    ->get();
        }

        // Caso não seja passado, retorna todos os usuários
        return User::with('perfil')
                ->orderBy('name', 'asc')
                ->get();
    }

    public function store(Request $request)
    {
        try {
            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'perfil' => 'required',
                'dataNascimentoUsuario' => 'required|date',
                'idCliente' => 'required|integer',
                'imgUsuario' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validação de imagem
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // Iniciando a transação
            DB::beginTransaction();

            // Verificar se o cliente existe
            $cliente = Clientes::find($request->idCliente);
            if (!$cliente) {
                throw new Exception("Cliente não encontrado!");
            }

            // Buscar o plano do cliente
            $plano = Planos::find($cliente->idPlano);
            if (!$plano) {
                throw new Exception("Plano do cliente não encontrado!");
            }

            // Contar o número de usuários existentes para o cliente
            $usuariosExistentes = User::where('idCliente', $request->idCliente)->count();

            // Verificar se o limite de usuários será ultrapassado
            if ($usuariosExistentes >= $plano->qtdeUsuarios) {
                throw new Exception("Limite de usuários atingido para o plano do cliente. Limite permitido: {$plano->qtdeUsuarios}.");
            }

            // Processando a imagem, caso tenha sido enviada
            $imgUsuarioPath = null;
            if ($request->hasFile('imgUsuario')) {
                $imgUsuarioPath = $request->file('imgUsuario')->store('images', 'public');
            }

            // Criando o usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password), // Certifique-se de criptografar a senha
                'perfil' => $request->perfil,
                'imgUsuario' => $imgUsuarioPath, // Salva o caminho da imagem
                'dataNascimentoUsuario' => $request->dataNascimentoUsuario,
                'usuarioAtivo' => true,
                'idCliente' => $request->idCliente,
            ]);

            // Commit da transação
            DB::commit();

            return response()->json([
                'message' => 'Usuário criado com sucesso!',
                'user' => $user,
            ], 201);

        } catch (Exception $e) {
            // Rollback da transação em caso de erro
            DB::rollBack();
            return response()->json([
                'error' => 'Erro ao criar usuário: ' . $e->getMessage(),
            ], 500);
        }
    }




    public function listarPastores(){

        $pastores = User::where('nivelUsuario', 3)->get();

        return $pastores;
    }

    public function show(string $id)
    {
        try{
            $user = User::with('perfil')->find($id);


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

    public function contarUsuarios(Request $request){

        $qtde_usuarios = User::where('idCliente', $request->idCliente)->count();

        return response()->json(['quantidade_usuarios' => $qtde_usuarios]);
    }

    public function gerarRelatorioUsuarios(Request $request)
    {

        $dataInicial = $request->dataInicial . ' 00:00:00';
        $dataFinal = $request->dataFinal . ' 23:59:59';

        try{

            $data_hoje = date("Y-m-d H:i");

            $usuarioCount = User::where('idCliente', $request->idCliente)->whereBetween('created_at', [$dataInicial, $dataFinal])->count();
            $usuariosAtivos = User::where('idCliente', $request->idCliente)->whereBetween('created_at', [$dataInicial, $dataFinal])->where('usuarioAtivo', 1)->count();
            $usuariosInativos = User::where('idCliente', $request->idCliente)->whereBetween('created_at', [$dataInicial, $dataFinal])->where('usuarioAtivo', 0)->count();

            $usuarios = User::where('idCliente', $request->idCliente)
            ->whereBetween('created_at', [$dataInicial, $dataFinal])
            ->orderBy('name', 'asc')
            ->get();

            if(!$usuarios){
                throw new Exception("Nenhum usuário encontrado!");
            }

        } catch(Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }

        return response()->json([
            'message' => 'Relatório gerado com sucesso!', 
            'titulo' => 'Relatório dos Membros da Primeira Igreja Batista de Presidente Prudente', 
            'qtdeUsuarios' => $usuarioCount,
            'qtdeUsuariosAtivos' => $usuariosAtivos,
            'qtdeUsuariosInativos' => $usuariosInativos,
            'usuarios' => $usuarios, 
            'data' => $data_hoje
            ],200
        );
    }

    
}
