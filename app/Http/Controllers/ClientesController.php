<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clientes;
use Exception;
use Stripe\Subscription;

class ClientesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Clientes::all();
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{

            $cliente = Clientes::create([
                'razaoSocialCliente' => $request->razaoSocialCliente,
                'email' => $request->email,
                'password' => $request->password,
                'idPlano' => $request->idPlano
            ]);

        } catch(Exception $e){
            return response()->json(['message' => 'Erro!']);
        }

        return response()->json(['message' => 'Sucesso']);
    }

    public function getStripeSubscription($stripeCustomerId)
    {
        try {
            // Configura sua chave secreta do Stripe
            $stripe = new \Stripe\StripeClient(config('stripe.test.sk'));
    
            // Obtém as assinaturas para o cliente
            $subscriptions = $stripe->subscriptions->all(['customer' => $stripeCustomerId]);
    
            if (empty($subscriptions->data)) {
                return response()->json(['message' => 'Nenhuma assinatura encontrada.'], 404);
            }
    
            // Pega a primeira assinatura
            $subscription = $subscriptions->data[0];
    
            // Extrai o plano da assinatura
            $plan = null;
            if (!empty($subscription->items->data)) {
                $price = $subscription->items->data[0]->price;
    
                // Verifica se o nickname está disponível
                if ($price->nickname) {
                    $plan = $price;
                } else {
                    // Busca o produto associado para obter o nome
                    $product = $stripe->products->retrieve($price->product);
                    $plan = array_merge((array)$price, ['nickname' => $product->name]);
                }
            }
    
            return response()->json([
                'subscription' => $subscription,
                'plan' => $plan, // Inclui informações sobre o plano com fallback para o nome do produto
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Clientes::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
