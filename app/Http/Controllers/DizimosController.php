<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dizimos;
use Exception;

class DizimosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Dizimos::where('idCliente', $request->idCliente)->get();
    }

    public function store(Request $request)
    {
        try {
            // Validações de campos obrigatórios
            if (!$request->dataCulto) {
                throw new Exception("A data do culto não pode estar vazia!");
            }

            // if (!$request->turnoCulto) {
            //     throw new Exception("O turno do culto não pode estar vazio!");
            // }

            if (!$request->valorArrecadado) {
                throw new Exception("O valor arrecadado não pode estar vazio!");
            }

            // Verifica se já existe um registro com a mesma data e turno
            $existingDizimo = Dizimos::where('dataCulto', $request->dataCulto)
                ->where('turnoCulto', $request->turnoCulto)
                ->first();

            if ($existingDizimo) {
                throw new Exception("Já existe um registro para esta data e turno!");
            }

            // Criação do novo registro
            $dizimo = Dizimos::create([
                'dataCulto' => $request->dataCulto,
                'turnoCulto' => $request->turnoCulto,
                'valorArrecadado' => $request->valorArrecadado,
                'idCliente' => $request->idCliente
            ]);

        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Erro ao salvar registro de dízimo', 'erro' => $e->getMessage()]);
        }

        return response()->json(['status' => 200, 'message' => 'Sucesso!', 'dizimo' => $dizimo]);
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
