<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use App\Models\Clientes;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Mail\DefineUserPasswordMail;

class PaymentController extends Controller
{
    public function processCheckout(Request $request) 
{
    \Log::info('Stripe Customer ID recebido:', ['stripe_customer_id' => $request->stripe_customer_id]);

    // Validação básica
    $request->validate([
        'stripe_customer_id' => 'required|string',
        'nome_plano' => 'required|string',
        'valor_plano' => 'required|numeric',
    ]);

    $cliente = Clientes::where('stripe_customer_id', $request->stripe_customer_id)->first();

    if (!$cliente) {
        return response()->json(['error' => 'Usuário não encontrado'], 404);
    }

    try {
        \Stripe\Stripe::setApiKey(config('stripe.test.sk'));

        $session = \Stripe\Checkout\Session::create([
            'customer' => $cliente->stripe_customer_id,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'brl',
                        'product_data' => [
                            'name' => $request->nome_plano,
                        ],
                        'unit_amount' => (int)($request->valor_plano * 100), // Valor em centavos
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => url('/success'),
            'cancel_url' => url('/checkout'),
        ]);

        return response()->json(['session_id' => $session->id]);

    } catch (\Exception $e) {
        \Log::error('Erro ao criar sessão de pagamento:', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Erro ao criar sessão de pagamento'], 500);
    }
}



    public function createSubscription(Request $request)
    {
        Stripe::setApiKey(config('stripe.test.sk'));

        $validatedData = $request->validate([
            'stripe_customer_id' => 'required|string', // O cliente Stripe ID
            'price_id' => 'required|string', // O ID do preço do produto no Stripe
        ]);

        try {
            $subscription = Subscription::create([
                'customer' => $validatedData['stripe_customer_id'],
                'items' => [[
                    'price' => $validatedData['price_id'],
                ]],
            ]);

            return response()->json([
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Backend em Laravel
    public function attachPaymentMethod(Request $request)
    {
        $validatedData = $request->validate([
            'payment_method_id' => 'required|string',
            'stripe_customer_id' => 'required|string',
        ]);

        try {
            // Configure a chave de API do Stripe
            \Stripe\Stripe::setApiKey(config('stripe.test.sk'));

            // Recupera e anexa o método de pagamento ao cliente
            $paymentMethod = \Stripe\PaymentMethod::retrieve($validatedData['payment_method_id']);
            $paymentMethod->attach([
                'customer' => $validatedData['stripe_customer_id'],
            ]);

            // Define o método de pagamento como padrão
            $customer = \Stripe\Customer::update(
                $validatedData['stripe_customer_id'],
                [
                    'invoice_settings' => [
                        'default_payment_method' => $validatedData['payment_method_id'],
                    ],
                ]
            );

            return response()->json(['success' => true, 'customer' => $customer], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function startSubscription(Request $request)
    {
        // IDs mockados para teste
        $customerId = $request->stripe_customer_id;
        $productId = $request->product_id;

        $cliente = Clientes::where('stripe_customer_id', $request->stripe_customer_id);

        try {

            \Stripe\Stripe::setApiKey(config('stripe.test.sk'));

            $prices = \Stripe\Price::all([
                'product' => $productId,
                'active' => true,
            ]);

            if (count($prices->data) === 0) {
                return response()->json(['success' => false, 'error' => 'Nenhum preço ativo encontrado para o produto.'], 400);
            }

            $priceId = $prices->data[0]->id;

            $subscription = \Stripe\Subscription::create([
                'customer' => $customerId,
                'items' => [
                    ['price' => $priceId],
                ],
                'expand' => ['latest_invoice.payment_intent'], // Expande para obter detalhes do pagamento
            ]);

            $cliente->statusPagamento = "Pago";
            $cliente->save();

            return response()->json(['success' => true, 'subscription' => $subscription], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }















    public function createCheckoutSession(Request $request)
    {
        // Validar os dados recebidos do frontend
        $request->validate([
            'stripe_customer_id' => 'required|string',
            'plano_id' => 'required|integer',
        ]);

        $cliente = Clientes::where('stripe_customer_id', $request->stripe_customer_id)->first();

        // Dados para o exemplo
        $products = [
            1 => ['name' => 'Plano Básico', 'amount' => 7990],  // valor em centavos
            2 => ['name' => 'Plano Padrão', 'amount' => 11990],
            3 => ['name' => 'Plano Premium', 'amount' => 19990],
        ];

        $planoId = $request->plano_id;
        if (!isset($products[$planoId])) {
            return response()->json(['error' => 'Plano inválido'], 400);
        }


        // Recuperar o ID do preço do plano
        $planPriceId = $this->getPlanPriceId($request->plano_id);

        // Configurar a chave secreta do Stripe
        \Stripe\Stripe::setApiKey(config('stripe.test.sk'));

        try {
            // Criar a sessão de checkout
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'customer' => $request->stripe_customer_id,
                'line_items' => [[
                    'price' => $planPriceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => url('http://localhost:5173/sucesso-checkout?stripe_customer_id='. $request->stripe_customer_id),
                'cancel_url' => url('http://localhost:5173/cancelar-checkout?stripe_customer_id='. $request->stripe_customer_id),
            ]);

            return response()->json(['session_id' => $session->id]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar a sessão de checkout: '. $e->getMessage()], 500);
        }
    }

    private function getPlanPriceId($planoId)
    {
        // Mapeie o ID do plano ao ID do preço no Stripe
        $planPriceMapping = [
            1 => 'price_1QbTo5JzC0nP78mHEKkQsWhv', // Substitua pelos seus price IDs do Stripe
            2 => 'price_1Qbm9QJzC0nP78mHAko4rXTt',
            3 => 'price_1Qbm9iJzC0nP78mHhFi8HLvg',
            // Adicione outros planos conforme necessário
        ];

        return $planPriceMapping[$planoId] ?? null;
    }

    public function deleteCustomer(Request $request)
    {
        // Verifica se o stripe_customer_id foi enviado
        $request->validate([
            'stripe_customer_id' => 'required|string',
        ]);

        // Busca o cliente com o stripe_customer_id
        $customer = Clientes::where('stripe_customer_id', $request->stripe_customer_id)->first();

        if ($customer) {
            // Deleta o cliente
            $customer->delete();

            // Retorna uma resposta de sucesso
            return response()->json(['message' => 'Cliente excluído com sucesso.'], 200);
        } else {
            // Caso o cliente não seja encontrado
            return response()->json(['error' => 'Cliente não encontrado.'], 404);
        }
    }

    public function successCheckout(Request $request)
    {
        // Verifica se o stripe_customer_id foi enviado
        $request->validate([
            'stripe_customer_id' => 'required|string',
        ]);

        // Busca o cliente com o stripe_customer_id
        $customer = Clientes::where('stripe_customer_id', $request->stripe_customer_id)->first();

        if ($customer) {
            // Deleta o cliente
            $customer->statusPagamento = "Pago";
            $customer->save();
            
             // Gerando token e data de expiração
             $token = Str::random(60);
             $tokenExpiration = now()->addHours(6);

            DB::table('password_reset_tokens')->insert([
                'email' => $customer->email,
                'token' => $token,
                'token_expiration' => $tokenExpiration,
                'tokenStatus' => 1
            ]);

            try{
                Mail::to($customer->email)->send(new DefineUserPasswordMail($token));
            } catch (\Exception $e) {
                \Log::error('Erro ao enviar e-mail: ' . $e->getMessage());
            }

            // Retorna uma resposta de sucesso
            return response()->json(['message' => 'Status alterado para pago e email enviado.'], 200);
        } else {
            // Caso o cliente não seja encontrado
            return response()->json(['error' => 'Cliente não encontrado.'], 404);
        }
    }



}



