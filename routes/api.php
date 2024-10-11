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


    // Usuários
    Route::get('/user', [UserController::class, 'index']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::post('/user', [UserController::class, 'store']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
    Route::patch('/deactivateUser/{id}', [UserController::class, 'deactivate']);
    Route::patch('/activateUser/{id}', [UserController::class, 'activate']);
    Route::get('/userCount', [UserController::class, 'contarUsuarios']);
    Route::get('/userReport', [UserController::class, 'gerarRelatorioUsuarios']);

    // Aulas EBD
    Route::get('/aulaEBD', [AulaEBDController::class, 'index']);
    Route::post('/aulaEBD', [AulaEBDController::class, 'store']);

    // Classes EBD
    Route::get('/classesEBD', [ClassesEBDController::class, 'index']);
    Route::post('/classesEBD', [ClassesEBDController::class, 'store']);

    // Dízimos
    Route::get('/dizimos', [DizimosController::class, 'index']);
    Route::post('/dizimos', [DizimosController::class, 'store']);

    // Locais
    Route::get('/locais', [LocaisController::class, 'index']);
    Route::post('/locais', [LocaisController::class, 'store']);

    // Eventos
    Route::get('/eventos', [EventosController::class, 'index']);
    Route::post('/eventos', [EventosController::class, 'store']);
    Route::get('/eventos/{id}', [EventosController::class, 'show']);
    Route::put('/eventos/{id}', [EventosController::class, 'update']);
    Route::get('/proximosEventos', [EventosController::class, 'listandoProximosEventos']);


    // Recurso
    Route::get('/recurso', [RecursoController::class, 'index']);
    Route::post('/recurso', [RecursoController::class, 'store']);
    Route::patch('/recurso/{id}/diminuirQuantidade', [RecursoController::class, 'diminuirQuantidade']);
    Route::patch('/recurso/{id}/aumentarQuantidade', [RecursoController::class, 'aumentarQuantidade']);
    Route::get('/recursoReport', [RecursoController::class, 'gerarRelatorioRecursos']);

    // Categoria Recurso
    Route::get('/categoriaRecurso', [CategoriaRecursoController::class, 'index']);
    Route::post('/categoriaRecurso', [CategoriaRecursoController::class, 'store']);

    // Tipo Recurso
    Route::get('/tipoRecurso', [TipoRecursoController::class, 'index']);
    Route::post('/tipoRecurso', [TipoRecursoController::class, 'store']);

    // Posts
    Route::get('/post', [PostController::class, 'index']);
    Route::post('/post', [PostController::class, 'store']);
    Route::get('/post/{id}', [PostController::class, 'show']);
    Route::put('/post/{id}', [PostController::class, 'update']);
    Route::patch('/post/{id}/desativar', [PostController::class, 'deactivate']);
    Route::patch('/post/{id}/ativar', [PostController::class, 'activate']);
    Route::get('/gerarRelatorioPosts', [PostController::class, 'gerarRelatorioPosts']);
    Route::get('/posts/pesquisar', [PostController::class, 'search']);

    // Departamentos
    Route::get('/departamentos', [DepartamentosController::class, 'index']);
    Route::post('/departamentos', [DepartamentosController::class, 'store']);
    Route::get('/departamentos/{id}', [DepartamentosController::class, 'show']);
    Route::put('/departamentos/{id}', [DepartamentosController::class, 'update']);
    Route::patch('/departamento/{id}/desativar', [DepartamentosController::class, 'deactivate']);
    Route::patch('/departamento/{id}/ativar', [DepartamentosController::class, 'activate']);
    Route::get('/departamentoReport', [DepartamentosController::class, 'gerarRelatorioDepartamentos']);

    // Tipo Post 
    Route::get('/tipoPost', [TipoPostController::class, 'index']); 
    Route::post('/tipoPost', [TipoPostController::class, 'store']); 
    Route::get('/tipoPost/{id}', [TipoPostController::class, 'show']); 
    Route::put('/tipoPost/{id}', [TipoPostController::class, 'update']); 
    Route::delete('/tipoPost/{id}', [TipoPostController::class, 'destroy']); 

    // Boletim
    Route::get('/boletim', [BoletinsController::class, 'index']);
    Route::post('/boletim', [BoletinsController::class, 'store']);
    Route::get('/boletim/{id}', [BoletinsController::class, 'show']);
    Route::put('/boletim/{id}', [BoletinsController::class, 'update']);
    Route::delete('/boletim/{id}', [BoletinsController::class, 'destroy']);
    Route::get('/boletins/pesquisar', [BoletinsController::class, 'search']);

    // Nivel Usuario
    Route::get('/nivelUsuario', [NivelUsuarioController::class, 'index']);
    Route::post('/nivelUsuario', [NivelUsuarioController::class, 'store']);
    Route::get('/nivelUsuario/{id}', [NivelUsuarioController::class, 'show']);
    Route::put('/nivelUsuario/{id}', [NivelUsuarioController::class, 'update']);

    // Missões
    Route::get('/missoes', [MissoesController::class, 'index']);
    Route::post('/missoes', [MissoesController::class, 'store']);
    Route::get('/missoes/{id}', [MissoesController::class, 'show']);
    Route::put('/missoes/{id}', [MissoesController::class, 'update']);
    Route::patch('/missoes/{id}/ativar', [MissoesController::class, 'activate']);
    Route::patch('/missoes/{id}/desativar', [MissoesController::class, 'deactivate']);
    Route::get('/missoesReport', [MissoesController::class, 'gerarRelatorioMissoes']);

    // Clientes
    Route::get('/clientes', [ClientesController::class, 'index']);
    Route::post('/clientes', [ClientesController::class, 'store']);

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);