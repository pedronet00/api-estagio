<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Celulas;
use Illuminate\Support\Facades\Validator;

class CelulasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $celulas =  Celulas::with('localizacao', 'responsavel')->where('idCliente', $request->idCliente)->get();

        return $celulas;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomeCelula' => 'required|string|max:255',
            'localizacaoCelula' => 'required|integer',
            'responsavelCelula' => 'required|integer',
            'diaReuniao' => 'required|integer', 
            'idCliente' => 'required|integer'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $celula = Celulas::create($request->all([
            'nomeCelula',
            'localizacaoCelula',
            'responsavelCelula',
            'diaReuniao',
            'idCliente',
        ]));
    
        return response()->json([
            'message' => 'Célula criada com sucesso!',
            'celula' => $celula,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
