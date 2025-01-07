<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Clientes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Str;



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

    if ($cliente && $cliente->statusPagamento != "Pago") {
        return response()->json([
            'message' => 'Ops! Parece que você tem um problema com o pagamento da mensalidade. Contate o suporte!'
        ], 403); // 403 Forbidden
    }

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
            'perfil' => 1 // Administrador master
        ]);
    }

    // Autenticar usuário
    $user = User::where('email', $data['email'])->first();

    if ($user && Hash::check($data['password'], $user->password)) {
        $cliente = Clientes::find($user->idCliente);

        if ($cliente->statusPagamento != "Pago") {
            return response()->json([
                'message' => 'Ops! Parece que você tem um problema com o pagamento da mensalidade. Contate o suporte!'
            ], 403); // 403 Forbidden
        }

        // Gerar token de autenticação para o usuário
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logado com sucesso como usuário!',
            'user' => $user,
            'token' => $token,
            'razaoSocial' => $cliente->razaoSocialCliente,
            'nome' => $user->name,
            'idCliente' => $user->idCliente,
            'idUsuario' => $user->id,
            'imgUsuario' => $user->imgUsuario,
            'perfil' => $user->perfil // Nível de usuário do usuário
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
            'idPlano' => 'required|integer', // Certifique-se de que o plano é fornecido
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['message' => 'Erro de validação: ', 'errors' => $e->errors()], 422);
    }

    try {
        // Configura a chave secreta do Stripe
        $stripe = new \Stripe\StripeClient(config('stripe.test.sk'));

        // Cria o cliente no Stripe
        $stripeCustomer = $stripe->customers->create([
            'name' => $validatedData['razaoSocialCliente'],
            'email' => $validatedData['email'],
        ]);

        // Cria o cliente no banco de dados
        $cliente = Clientes::create([
            'razaoSocialCliente' => $validatedData['razaoSocialCliente'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'idPlano' => $validatedData['idPlano'],
            'session_token' => Str::random(40), // Token único
            'statusPagamento' => "Pendente",
            'stripe_customer_id' => $stripeCustomer->id, // Salva o ID do cliente do Stripe
        ]);

        return response()->json([
            'message' => 'Cliente registrado com sucesso',
            'session_token' => $cliente->session_token,
            'plano' => $cliente->idPlano,
            'stripe_customer_id' => $stripeCustomer->id,
        ], 201);

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
