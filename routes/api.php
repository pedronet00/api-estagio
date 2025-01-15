<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// imports controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\NivelUsuarioController;
use App\Http\Controllers\TipoPostController;
use App\Http\Controllers\BoletinsController;
use App\Http\Controllers\DepartamentosController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MissoesController;
use App\Http\Controllers\CategoriaRecursoController;
use App\Http\Controllers\TipoRecursoController;
use App\Http\Controllers\RecursoController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\LocaisController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\DizimosController;
use App\Http\Controllers\ClassesEBDController;
use App\Http\Controllers\AulaEBDController;
use App\Http\Controllers\LivrosController;
use App\Http\Controllers\FinancasController;
use App\Http\Controllers\EntradasController;
use App\Http\Controllers\SaidasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CelulasController;
use App\Http\Controllers\EncontrosCelulasController;
use App\Http\Controllers\MembrosCelulasController;
use App\Http\Controllers\PerfisController;
use App\Http\Controllers\FuncoesController;
use App\Http\Controllers\PerfisFuncoesController;
use App\Http\Controllers\PlanosController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CultoController;
use App\Http\Controllers\EscalasCultosController;
use App\Http\Controllers\FuncoesCultoController;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;


Route::post('/process-payment', [PaymentController::class, 'processPayment']);
Route::post('/attach-payment-method', [PaymentController::class, 'attachPaymentMethod']);
Route::post('/start-subscription', [PaymentController::class, 'startSubscription']);


Route::post('/create-checkout-session', [PaymentController::class, 'createCheckoutSession']);
Route::post('/delete-customer', [PaymentController::class, 'deleteCustomer']);
Route::post('/success-checkout', [PaymentController::class, 'successCheckout']);

Route::get('/enviarEmailTeste', [AuthController::class, 'enviarEmailsTestes']);
// Route::middleware('auth:sanctum')->group(function () {

    // Usuários
    Route::get('/user', [UserController::class, 'index']);
    Route::post('/user', [UserController::class, 'store']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
    Route::patch('/deactivateUser/{id}', [UserController::class, 'deactivate']);
    Route::patch('/activateUser/{id}', [UserController::class, 'activate']);
    Route::get('/userCount', [UserController::class, 'contarUsuarios']);
    Route::get('/userReport', [UserController::class, 'gerarRelatorioUsuarios']);

    // Finanças
    Route::get('/financas/saldo-mensal', [FinancasController::class, 'saldoMensal']);
    Route::get('/financas/entradasSaidasMensal', [FinancasController::class, 'entradasSaidasMensais']);
    Route::get('/financasReport', [FinancasController::class, 'gerarRelatorioFinancas']);

    // Entradas
    Route::get('/entradas', [EntradasController::class, 'index']);
    Route::post('/entradas', [EntradasController::class, 'store']);
    Route::delete('/entradas/{id}', [EntradasController::class, 'delete']);

    // Saídas
    Route::get('/saidas', [SaidasController::class, 'index']);
    Route::post('/saidas', [SaidasController::class, 'store']);
    Route::delete('/saidas/{id}', [SaidasController::class, 'delete']);


    // Livros
    Route::get('/livros', [LivrosController::class, 'index']);
    Route::post('/livros', [LivrosController::class, 'store']);
    Route::get('/livros/{id}/download', [LivrosController::class, 'download']);

    // Aulas EBD
    Route::get('/aulaEBD', [AulaEBDController::class, 'index']);
    Route::post('/aulaEBD', [AulaEBDController::class, 'store']);
    Route::get('/ebdReport', [AulaEBDController::class, 'gerarRelatorioEBD']);

    // Classes EBD
    Route::get('/classesEBD', [ClassesEBDController::class, 'index']);
    Route::post('/classesEBD', [ClassesEBDController::class, 'store']);
    Route::get('/classesEBD/{id}', [ClassesEBDController::class, 'show']);
    Route::put('/classesEBD/{id}', [ClassesEBDController::class, 'update']);

    // Dízimos
    Route::get('/dizimos', [DizimosController::class, 'index']);
    Route::get('/dizimos/{id}', [DizimosController::class, 'show']);
    Route::put('/dizimos/{id}', [DizimosController::class, 'update']);
    Route::post('/dizimos', [DizimosController::class, 'store']);
    Route::delete('/dizimos/{id}', [DizimosController::class, 'destroy']);
    
    // Recurso
    Route::get('/recurso', [RecursoController::class, 'index']);
    Route::get('/recurso/{id}', [RecursoController::class, 'show']);
    Route::post('/recurso', [RecursoController::class, 'store']);
    Route::put('/recurso/{id}', [RecursoController::class, 'update']);
    Route::delete('/recurso/{id}', [RecursoController::class, 'destroy']);
    Route::patch('/recurso/{id}/diminuirQuantidade', [RecursoController::class, 'diminuirQuantidade']);
    Route::patch('/recurso/{id}/aumentarQuantidade', [RecursoController::class, 'aumentarQuantidade']);
    Route::get('/recursoReport', [RecursoController::class, 'gerarRelatorioRecursos']);
    
    // Categoria Recurso
    Route::get('/categoriaRecurso', [CategoriaRecursoController::class, 'index']);
    Route::post('/categoriaRecurso', [CategoriaRecursoController::class, 'store']);
    
    // Tipo Recurso
    Route::get('/tipoRecurso', [TipoRecursoController::class, 'index']);
    Route::post('/tipoRecurso', [TipoRecursoController::class, 'store']);
    
    // Departamentos
    Route::get('/departamentos', [DepartamentosController::class, 'index']);
    Route::post('/departamentos', [DepartamentosController::class, 'store']);
    Route::get('/departamentos/{id}', [DepartamentosController::class, 'show']);
    Route::put('/departamentos/{id}', [DepartamentosController::class, 'update']);
    Route::patch('/departamento/{id}/desativar', [DepartamentosController::class, 'deactivate']);
    Route::patch('/departamento/{id}/ativar', [DepartamentosController::class, 'activate']);
    Route::get('/departamentoReport', [DepartamentosController::class, 'gerarRelatorioDepartamentos']);
    
    
    // Missões
    Route::get('/missoes', [MissoesController::class, 'index']);
    Route::post('/missoes', [MissoesController::class, 'store']);
    Route::get('/missoes/{id}', [MissoesController::class, 'show']);
    Route::put('/missoes/{id}', [MissoesController::class, 'update']);
    Route::patch('/missoes/{id}/ativar', [MissoesController::class, 'activate']);
    Route::patch('/missoes/{id}/desativar', [MissoesController::class, 'deactivate']);
    Route::get('/missoesReport', [MissoesController::class, 'gerarRelatorioMissoes']);
    Route::get('/dizimosReport', [DizimosController::class, 'gerarRelatorioDizimos']);
    
    // Clientes
    Route::post('/clientes', [ClientesController::class, 'store']);
    // Eventos
    Route::get('/eventos', [EventosController::class, 'index']);
    Route::post('/eventos', [EventosController::class, 'store']);
    Route::get('/eventos/{id}', [EventosController::class, 'show']);
    Route::delete('/eventos/{id}', [EventosController::class, 'destroy']);
    Route::put('/eventos/{id}', [EventosController::class, 'update']);
    Route::get('/proximosEventos', [EventosController::class, 'listandoProximosEventos']);
    Route::get('/eventosReport', [EventosController::class, 'gerarRelatorioEventos']);
    Route::get('/clientes', [ClientesController::class, 'index']);
    Route::get('/clientes/{id}', [ClientesController::class, 'show']);
    Route::get('/stripe/subscription/{stripeCustomerId}', [ClientesController::class, 'getStripeSubscription']);


    // Locais
    Route::get('/locais', [LocaisController::class, 'index']);
    Route::post('/locais', [LocaisController::class, 'store']);
    Route::get('/locais/{id}', [LocaisController::class, 'show']);
    Route::put('/locais/{id}', [LocaisController::class, 'update']);
    Route::delete('/locais/{id}', [LocaisController::class, 'destroy']);
    Route::patch('/locais/{id}/desativar', [LocaisController::class, 'deactivate']);
    Route::patch('/locais/{id}/ativar', [LocaisController::class, 'activate']);

    // Posts
    Route::get('/post', [PostController::class, 'index']);
    Route::post('/post', [PostController::class, 'store']);
    Route::get('/post/{id}', [PostController::class, 'show']);
    Route::put('/post/{id}', [PostController::class, 'update']);
    Route::patch('/post/{id}/desativar', [PostController::class, 'deactivate']);
    Route::patch('/post/{id}/ativar', [PostController::class, 'activate']);
    Route::get('/gerarRelatorioPosts', [PostController::class, 'gerarRelatorioPosts']);
    Route::get('/posts/pesquisar', [PostController::class, 'search']);

    // Tipo Post 
    Route::get('/tipoPost', [TipoPostController::class, 'index']); 
    Route::post('/tipoPost', [TipoPostController::class, 'store']); 
    Route::get('/tipoPost/{id}', [TipoPostController::class, 'show']); 
    Route::put('/tipoPost/{id}', [TipoPostController::class, 'update']); 
    Route::delete('/tipoPost/{id}', [TipoPostController::class, 'destroy']); 

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Nivel Usuario
    Route::get('/nivelUsuario', [NivelUsuarioController::class, 'index']);
    Route::post('/nivelUsuario', [NivelUsuarioController::class, 'store']);
    Route::get('/nivelUsuario/{id}', [NivelUsuarioController::class, 'show']);
    Route::put('/nivelUsuario/{id}', [NivelUsuarioController::class, 'update']);

    // Boletim
    Route::get('/boletim', [BoletinsController::class, 'index']);
    Route::post('/boletim', [BoletinsController::class, 'store']);
    Route::get('/boletim/{id}', [BoletinsController::class, 'show']);
    Route::put('/boletim/{id}', [BoletinsController::class, 'update']);
    Route::delete('/boletim/{id}', [BoletinsController::class, 'destroy']);
    Route::get('/boletins/pesquisar', [BoletinsController::class, 'search']);
    Route::get('/boletins/semana', [BoletinsController::class, 'getWeeklyCultos']);

    // Dashboard
    Route::get('/dashboardData', [DashboardController::class, 'index']);
    Route::get('/pastores', [UserController::class, 'listarPastores']);

    // Células
    Route::get('/celulas', [CelulasController::class, 'index']);
    Route::get('/celulas/{id}', [CelulasController::class, 'show']);
    Route::post('/celulas', [CelulasController::class, 'store']);
    Route::put('/celulas/{id}', [CelulasController::class, 'update']);
    Route::delete('/celulas/{id}', [CelulasController::class, 'destroy']);
    Route::get('/membrosCount', [CelulasController::class, 'contarMembrosCelula']);

    // Encontros células
    Route::get('/encontrosCelulas', [EncontrosCelulasController::class, 'index']);
    Route::post('/encontrosCelulas', [EncontrosCelulasController::class, 'store']);
    Route::post('/encontrosCelulas/registrarPresentes', [EncontrosCelulasController::class, 'registrarPresentes']);
    Route::delete('/encontrosCelulas/{id}', [EncontrosCelulasController::class, 'destroy']);
    Route::get('/proximoEncontroCelula/{id}', [EncontrosCelulasController::class, 'proximoEncontro']);

    // Membros células
    Route::get('/membrosCelulas', [MembrosCelulasController::class, 'index']);
    Route::get('/obterResponsavelCelula', [MembrosCelulasController::class, 'obterResponsavel']);
    Route::post('/membrosCelulas', [MembrosCelulasController::class, 'store']);
    Route::delete('/membrosCelulas/{id}', [MembrosCelulasController::class, 'destroy']);

    // Perfis
    Route::get('/perfis', [PerfisController::class, 'index']);
    Route::post('/perfis', [PerfisController::class, 'store']);
    Route::get('/perfis/{id}', [PerfisController::class, 'show']);

    // Funções
    Route::get('/funcoes', [FuncoesController::class, 'index']);
    Route::post('/funcoes', [FuncoesController::class, 'store']);

    // Perfis-Funções
    Route::get('/perfis-funcoes', [PerfisFuncoesController::class, 'index']);
    Route::post('/perfis-funcoes', [PerfisFuncoesController::class, 'store']);
    Route::get('/perfis-funcoes/{id}', [PerfisFuncoesController::class, 'show']);
    Route::get('/perfis-funcoes/{idPerfil}/{idFuncao}', [PerfisFuncoesController::class, 'showPerfilFuncao']);
    Route::get('/perfis-funcoes/cliente/{idCliente}', [PerfisFuncoesController::class, 'showPerfilFuncaoCliente']);
    Route::patch('/perfis-funcoes/{idPerfil}/{idFuncao}/desativar', [PerfisFuncoesController::class, 'desativarFuncaoPerfil']);
    Route::patch('/perfis-funcoes/{idPerfil}/{idFuncao}/ativar', [PerfisFuncoesController::class, 'ativarFuncaoPerfil']);

    // Planos
    Route::get('/planos', [PlanosController::class, 'index']);
    Route::post('/planos', [PlanosController::class, 'store']);

    // Culto
    Route::get('/culto', [CultoController::class, 'index']);
    Route::post('/culto', [CultoController::class, 'store']);
    Route::put('/culto', [CultoController::class, 'update']);
    Route::get('/culto/{id}', [CultoController::class, 'show']);
    Route::delete('/culto', [CultoController::class, 'destroy']);
    Route::get('/cultoReport', [CultoController::class, 'cultoReport']);

    // FuncoesCulto
    Route::get('/funcoes-culto', [FuncoesCultoController::class, 'index']);
    Route::post('/funcoes-culto', [FuncoesCultoController::class, 'store']);
    Route::get('/funcoes-culto/{id}', [FuncoesCultoController::class, 'show']);

    // EscalasCulto
    Route::get('/escalas-cultos', [EscalasCultosController::class, 'index']);
    Route::post('/escalas-cultos', [EscalasCultosController::class, 'store']);
    Route::put('/escalas-cultos', [EscalasCultosController::class, 'update']);
    Route::get('/escala-culto/{idEscala}', [EscalasCultosController::class, 'show']);
    Route::get('/escala-culto-usuario', [EscalasCultosController::class, 'mostrarEscalasUsuario']);
    Route::delete('/escala-culto', [EscalasCultosController::class, 'destroy']);

    Route::post('/mercadopago/client/create', [AuthController::class, 'createCustomer']);// No arquivo routes/api.php
    Route::get('/mercadopago/clientes', [AuthController::class, 'listClients']);
    Route::get('/mercadopago/createSubscription', [AuthController::class, 'createSubscription']);

    Route::get('/send-test-email', function () {
        Mail::to('stabilepedro010403@gmail.com')->send(new TestMail());
        return 'Test email sent!';
    });
    

        
    
// });
