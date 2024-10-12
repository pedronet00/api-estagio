<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Entradas;

class EntradasController extends Controller
{
    public function index(Request $request)
    {
        $entradas = Entradas::where('idCliente', $request->idCliente)->with('cliente')->orderBy('data', 'desc')->get();
        return response()->json($entradas);
    }

    public function store(Request $request)
    {
        $entrada = Entradas::create($request->all());
        return response()->json($entrada, 201);
    }
}
