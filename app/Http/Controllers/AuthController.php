<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function login(Request $request)
{
    $data = $request->validate([
        'email' => ['required', 'email', 'exists:users'],
        'password' => ['required', 'min:6']
    ]);

    if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
        return response()->json([
            'message' => 'Credenciais inválidas',
        ], 401);
    }

    $user = Auth::user();

    $token = $user->createToken('auth_token')->plainTextToken;
    $name = $user->name;
    $idUser = $user->id;

    return response()->json([
        'message' => 'Logado com sucesso!',
        'user' => $user,
        'token' => $token,
        'name' => $name,
        'idUser' => $idUser
    ]);
}

    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json(['message' => 'Deslogado com sucesso!']);
    }
}
