<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class PaymentController extends Controller
{
    public function pagamentoCartao(Request $request)
    {
        $client = new PaymentClient();

    try {

        // Step 4: Create the request array
        $request = [
            "transaction_amount" => 100,
            "token" => "YOUR_CARD_TOKEN",
            "description" => "description",
            "installments" => 1,
            "payment_method_id" => "visa",
            "payer" => [
                "email" => "user@test.com",
            ]
        ];

        // Step 5: Create the request options, setting X-Idempotency-Key
        $request_options = new RequestOptions();
        $request_options->setCustomHeaders(["X-Idempotency-Key: <SOME_UNIQUE_VALUE>"]);

        // Step 6: Make the request
        $payment = $client->create($request, $request_options);
        echo $payment->id;

    // Step 7: Handle exceptions
    } catch (MPApiException $e) {
        echo "Status code: " . $e->getApiResponse()->getStatusCode() . "\n";
        echo "Content: ";
        var_dump($e->getApiResponse()->getContent());
        echo "\n";
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
    }

    public function pagamentoPix(Request $request)
    {
        // Criar instância de pagamento Pix
        $client = new PaymentClient();
        $payment = new Paymente();
        $payment->transaction_amount = $request->valor;
        $payment->description = "Pagamento de mensalidade";
        $payment->payment_method_id = "pix";
        $payment->payer = array(
            "email" => $request->email
        );

        $payment->save();

        if ($payment->status == 'pending') {
            return response()->json(['pix_qr_code' => $payment->point_of_interaction->transaction_data->qr_code]);
        }

        return response()->json(['status' => 'Falha no pagamento']);
    }
}

