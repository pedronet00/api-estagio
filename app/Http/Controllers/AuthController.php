<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Clientes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;
use Exception;



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
       try{

        $validated = $request->validate([
            'razaoSocialCliente' => 'required|string|max:255',
            'dominioCliente' => 'required|string|max:255',
            'emailCliente' => 'required|email|max:255|unique:clientes',
            'passwordCliente' => 'required|string|min:8',
        ]);

        Clientes::create([
            'razaoSocialCliente' => $validated['razaoSocialCliente'],
            'dominioCliente' => $validated['dominioCliente'],
            'emailCliente' => $validated['emailCliente'],
            'passwordCliente' => bcrypt($validated['passwordCliente']),
        ]);

        // Criação do tenant
        $tenant = Tenant::create(['id' => $request->dominioCliente]);
        $tenant->domains()->create(['domain' => "{$request->dominioCliente}.localhost"]);

       } catch(Exception $e){
            
        return response()->json(['message' => 'Falha ao registrar cliente', 'error' => $e->getMessage()], 500);
       }

        return response()->json(['message' => 'Cliente registrado com sucesso!'], 201);
    }


    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json(['message' => 'Deslogado com sucesso!']);
    }
}
