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


// Usuários
Route::get('/user', [UserController::class, 'index']);
Route::get('/user/{id}', [UserController::class, 'show']);
Route::post('/user', [UserController::class, 'store']);
Route::put('/user/{id}', [UserController::class, 'update']);
Route::delete('/user/{id}', [UserController::class, 'destroy']);
Route::patch('/deactivateUser/{id}', [UserController::class, 'deactivate']);
Route::patch('/activateUser/{id}', [UserController::class, 'activate']);
Route::get('/gerarRelatorioUsuarios', [UserController::class, 'gerarRelatorioUsuarios']);
Route::get('/gerarRelatorioPastores', [UserController::class, 'gerarRelatorioPastores']);

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

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

Route::get('/nivelUsuario', [NivelUsuarioController::class, 'index']);
Route::post('/nivelUsuario', [NivelUsuarioController::class, 'store']);
