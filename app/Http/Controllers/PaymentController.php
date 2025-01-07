<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use App\Models\Clientes;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        // Recebe os dados do plano (nome e valor)
        $paymentMethod = $request->input('payment_method');
        $nomePlano = $request->input('nome_plano');  // Nome do plano
        $valorPlano = $request->input('valor_plano');  // Valor do plano (em reais)

        $sessionToken = $request->session_token;

        // Encontra o usuário
        $cliente = Clientes::where('session_token', $sessionToken)->first();

        if (!$cliente) {
            return response()->json(['error' => 'Usuário não encontrado.', 'token' => $sessionToken], 404);
        }

        // Convertendo o valor para centavos
        $valorPlanoCentavos = (int) ($valorPlano * 100);

        // Configura a chave da API do Stripe
        Stripe::setApiKey(config('stripe.test.sk'));

        try {
            // Criação da sessão de pagamento
            $session = Session::create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'brl',  // Usando BRL para reais
                            'product_data' => [
                                'name' => $nomePlano, // Nome do produto vindo do frontend
                            ],
                            'unit_amount' => $valorPlanoCentavos,  // Valor em centavos
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => url('/success'),
                'cancel_url' => url('/checkout'),
            ]);

            // Atualiza o status do pagamento e o próximo pagamento
            $cliente->statusPagamento = "Pago";
            $cliente->proximoPagamento = now()->addMonth(); // Define próximo pagamento para daqui a 1 mês
            $cliente->save();

            return response()->json([
                'session_id' => $session->id, 
                'message' => 'Pagamento processado com sucesso!',
                'produto' => $nomePlano,
                'valor' => $valorPlano,
                'proximoPagamento' => $cliente->proximoPagamento->toDateString(),
            ], 200);
        } catch (ApiErrorException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
        $customerId = $request->stripe_customer_id; // ID do cliente no Stripe
        $productId = $request->product_id; // ID do produto no Stripe

        try {
            // Configure a chave de API do Stripe
            \Stripe\Stripe::setApiKey(config('stripe.test.sk'));

            // Recupera o preço relacionado ao produto
            $prices = \Stripe\Price::all([
                'product' => $productId,
                'active' => true,
            ]);

            if (count($prices->data) === 0) {
                return response()->json(['success' => false, 'error' => 'Nenhum preço ativo encontrado para o produto.'], 400);
            }

            $priceId = $prices->data[0]->id; // Pega o primeiro preço ativo

            // Cria a assinatura
            $subscription = \Stripe\Subscription::create([
                'customer' => $customerId,
                'items' => [
                    ['price' => $priceId],
                ],
                'expand' => ['latest_invoice.payment_intent'], // Expande para obter detalhes do pagamento
            ]);

            return response()->json(['success' => true, 'subscription' => $subscription], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }





}



