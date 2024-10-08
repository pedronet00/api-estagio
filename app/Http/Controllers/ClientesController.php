<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clientes;
use Exception;

class ClientesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Clientes::all();
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{

            $cliente = Clientes::create([
                'razaoSocialCliente' => $request->razaoSocialCliente,
                'email' => $request->email,
                'password' => $request->password
            ]);

        } catch(Exception $e){
            return response()->json(['message' => 'Erro!']);
        }

        return response()->json(['message' => 'Sucesso']);
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
