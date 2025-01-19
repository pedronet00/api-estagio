<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

use MercadoPago\MercadoPagoConfig;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email-preview', function () {
    return view('mailing.redefine_password'); // Retorna diretamente a view do e-mail
});

Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');

Route::get('/test', function () {
    
MercadoPagoConfig::setAccessToken('<ACCESS_TOKEN>');
    return 'SDK carregado com sucesso!';
});

Route::post('/pagamento/cartao', [PaymentController::class, 'pagamentoCartao']);
Route::post('/pagamento/pix', [PaymentController::class, 'pagamentoPix'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

