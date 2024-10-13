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

        // Tente encontrar o cliente com o e-mail fornecido
        $cliente = Clientes::where('email', $data['email'])->first();

        // Verifica se um cliente foi encontrado
        if ($cliente && Hash::check($data['password'], $cliente->password)) {
            // Login bem-sucedido para cliente

            $idCliente = $cliente->id;
            $razaoSocial = $cliente->razaoSocialCliente;

            return response()->json([
                'message' => 'Logado com sucesso como cliente!',
                'user' => $cliente,
                'idCliente' => $idCliente,
                'razaoSocial' => $razaoSocial,
                'nivelUsuario' => 4 // Nível de usuário para clientes
            ]);
        }

        // Se não encontrar um cliente, tente encontrar um usuário
        $user = User::where('email', $data['email'])->first();

        // Verifica se um usuário foi encontrado
        if ($user && Hash::check($data['password'], $user->password)) {
            // Login bem-sucedido para usuário

            $idCliente = $user->idCliente;

            $cliente = Clientes::find($idCliente);

            $razaoSocial = $cliente->razaoSocialCliente;

            return response()->json([
                'message' => 'Logado com sucesso como usuário!',
                'user' => $user,
                'idCliente' => $idCliente,
                'razaoSocial' => $razaoSocial,
                'nivelUsuario' => $user->nivelUsuario // Nível de usuário do usuário
            ]);
        }

        // Se nenhum cliente ou usuário foi autenticado
        return response()->json([
            'message' => 'Credenciais inválidas',
        ], 401);
    }



    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'razaoSocialCliente' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clientes',
            'password' => 'required|string|min:6',
        ]);

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
