<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Clientes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http; // Importação correta do Http
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



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
                'cnpj' => 'required',
                'email' => 'required|string|email|max:255|unique:clientes',
                'idPlano' => 'required|integer',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Erro de validação: ', 'errors' => $e->errors()], 422);
        }

        try {
            $stripe = new \Stripe\StripeClient(config('stripe.test.sk'));

            $stripeCustomer = $stripe->customers->create([
                'name' => $validatedData['razaoSocialCliente'],
                'email' => $validatedData['email'],
            ]);

            $randomPassword = Str::random(24);

            $cliente = Clientes::create([
                'razaoSocialCliente' => $validatedData['razaoSocialCliente'],
                'email' => $validatedData['email'],
                'password' => Hash::make($randomPassword),
                'idPlano' => $validatedData['idPlano'],
                'session_token' => Str::random(40),
                'statusPagamento' => "Pendente",
                'stripe_customer_id' => $stripeCustomer->id,
                'cnpj' => $validatedData['cnpj']
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

    public function enviarEmailsTestes()
    {
        try {
            Mail::to("stabilepedro010403@gmail.com")->send(new TestMail());
        } catch (\Exception $e) {
            // Log error or handle it gracefully
            \Log::error('Erro ao enviar e-mail: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json(['message' => 'Deslogado com sucesso!']);
    }

    public function verifyResetToken($token)
    {
        // Buscar o token na tabela password_reset_tokens
        $tokenData = DB::table('password_reset_tokens')
                    ->where('token', $token)
                    ->first();

        if($tokenData->tokenStatus == 0){
            return response()->json(['Erro' => 'Token inválido'], 400);
        }

        // Verificar se o token existe e se ainda é válido
        if ($tokenData && Carbon::parse($tokenData->token_expiration)->isFuture()) {
            return response()->json([
                'message' => 'Token válido.',
                'email' => $tokenData->email,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Token inválido ou expirado.',
            ], 400);
        }
    }

    public function updatePassword(Request $request, string $email)
    {
        try {
            // Encontrar o usuário pelo email
            $user = User::where('email', $email)->first();

            if ($user) {
                // Atualizar a senha do usuário
                $user->password = bcrypt($request->password); // Criptografar a senha antes de salvar
                $user->save();
            } else {
                // Se o usuário não for encontrado, procurar no modelo Clientes
                $cliente = Clientes::where('email', $email)->first();

                if ($cliente) {
                    // Atualizar a senha do cliente
                    $cliente->password = bcrypt($request->password); // Criptografar a senha antes de salvar
                    $cliente->save();
                } else {
                    // Se nenhum usuário ou cliente for encontrado, retornar erro
                    return response()->json(['erro' => 'Não existe usuário ou cliente com esse email.'], 422);
                }
            }

            // Atualizar o tokenStatus na tabela password_reset_tokens
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->update(['tokenStatus' => 0]);

        } catch (Exception $e) {
            return response()->json(['Erro' => $e->getMessage()], 500);
        }

        return response()->json(['Sucesso' => 'Senha atualizada com sucesso.'], 200);
    }

}
