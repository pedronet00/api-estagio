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
        // Validação dos dados de entrada
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6']
        ]);

        // Autenticar cliente
        $cliente = Clientes::where('email', $data['email'])->first();

        if ($cliente && Hash::check($data['password'], $cliente->password)) {
            // Gerar token de autenticação para o cliente
            $token = $cliente->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Logado com sucesso como cliente!',
                'user' => $cliente,
                'token' => $token,
                'razaoSocial' => $cliente->razaoSocialCliente,
                'idCliente' => $cliente->id,
                'idUsuario' => $cliente->id,
                'nivelUsuario' => 4 // Nível de usuário para clientes
            ]);
        }

        // Autenticar usuário
        $user = User::where('email', $data['email'])->first();

        if ($user && Hash::check($data['password'], $user->password)) {
            // Gerar token de autenticação para o usuário
            $token = $user->createToken('auth_token')->plainTextToken;

            $cliente = Clientes::find($user->idCliente);
            $razaoSocial = $cliente->razaoSocialCliente;

            return response()->json([
                'message' => 'Logado com sucesso como usuário!',
                'user' => $user,
                'token' => $token,
                'razaoSocial' => $razaoSocial,
                'idCliente' => $user->idCliente,
                'idUsuario' => $user->id,
                'nivelUsuario' => $user->nivelUsuario // Nível de usuário do usuário
            ]);
        }

        return response()->json(['message' => 'Credenciais inválidas'], 401);
    }




    public function register(Request $request)
    {
        
        try {
            $validatedData = $request->validate([
                'razaoSocialCliente' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:clientes',
                'password' => 'required|string|min:6',
            ]);
    
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Erro de validação: ', 'errors' => $e->errors()], 422);
        }

        try {
            $cliente = Clientes::create([
                'razaoSocialCliente' => $validatedData['razaoSocialCliente'],
                'email' => $validatedData['email'], // Certifique-se de que está capturando o 'email' corretamente
                'password' => Hash::make($validatedData['password']),
            ]);

            return response()->json(['message' => 'Cliente registrado com sucesso'], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Falha ao registrar cliente', 'error' => $e->getMessage()], 500);
        }
    }



    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json(['message' => 'Deslogado com sucesso!']);
    }
}
