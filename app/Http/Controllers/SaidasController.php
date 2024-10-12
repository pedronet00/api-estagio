<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Saidas;

class SaidasController extends Controller
{
    public function index(Request $request)
    {
        $saidas = Saidas::where('idCliente', $request->idCliente)->with('cliente')->orderBy('data', 'desc')->get();
        return response()->json($saidas);
    }

    public function store(Request $request)
    {
        $saida = Saidas::create($request->all());
        return response()->json($saida, 201);
    }
}
