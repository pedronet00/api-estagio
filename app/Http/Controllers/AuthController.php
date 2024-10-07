<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Clientes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;



class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users'],
            'password' => ['required', 'min:6']
        ]);

        if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return response()->json([
                'message' => 'Credenciais inválidas',
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;
        $name = $user->name;
        $idUser = $user->id;
        $nivelUsuario = $user->nivelUsuario;

        return response()->json([
            'message' => 'Logado com sucesso!',
            'user' => $user,
            'token' => $token,
            'name' => $name,
            'idUser' => $idUser,
            'nivelUsuario' => $nivelUsuario
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'razaoSocialCliente' => ['required', 'string', 'max:255'],
            'dominioCliente' => ['required', 'string', 'unique:tenants'] // Certifique-se de que o domínio seja único
        ]);

        // Criação do cliente
        $cliente = Clientes::create([
            'razaoSocialCliente' => $data['razaoSocialCliente'],
            'dominioCliente' => $data['dominioCliente']
        ]);

        // Criação do tenant
        $tenant = Tenant::create(['id' => $cliente->dominioCliente]);
        $tenant->domains()->create(['domain' => "{$request->dominioCliente}.localhost"]);

        


        return response()->json([
            'message' => 'Registrado com sucesso!'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json(['message' => 'Deslogado com sucesso!']);
    }
}
