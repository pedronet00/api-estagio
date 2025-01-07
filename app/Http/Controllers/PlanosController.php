<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Planos;

class PlanosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Planos::all();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{

            $plano = Planos::create([
                'nomePlano' => $request->nomePlano,
                'qtdeUsuarios' => $request->qtdeUsuarios,
                'qtdeDepartamentos' => $request->qtdeDepartamentos,
                'qtdeMissoes' => $request->qtdeMissoes,
                'qtdeCelulas' => $request->qtdeCelulas
            ]);

        } catch(Exception $e){
            return response()->json(['erro' => $e->getMessage()]);
        }

        return response()->json(['sucesso' => $plano]);
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
