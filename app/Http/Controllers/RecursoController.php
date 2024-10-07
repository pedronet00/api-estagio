<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recursos;
use Exception;

class RecursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Recursos::with(['tipo', 'categoria'])->get();
    }

    public function store(Request $request)
    {
        try {
            if (!$request->nomeRecurso) {
                throw new Exception("Você deve preencher o nome do recurso!");
            }

            if (!$request->tipoRecurso) {
                throw new Exception("Você deve preencher o tipo do recurso!");
            }

            if (!$request->categoriaRecurso) {
                throw new Exception("Você deve preencher a categoria do recurso!");
            }

            if (!$request->quantidadeRecurso) {
                throw new Exception("Você deve preencher a quantidade do recurso!");
            }

            $recurso = Recursos::create([
                'nomeRecurso' => $request->nomeRecurso,
                'tipoRecurso' => $request->tipoRecurso,
                'categoriaRecurso' => $request->categoriaRecurso,
                'quantidadeRecurso' => $request->quantidadeRecurso,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['status' => 'sucesso!', 'recurso' => $recurso], 201);
    }

    /**
     * Método para aumentar a quantidade de um recurso.
     */
    public function aumentarQuantidade(string $id)
    {
        try {
            $recurso = Recursos::findOrFail($id);
    
            // Aumenta a quantidade do recurso em 1
            $recurso->quantidadeRecurso += 1;
            $recurso->save();
    
            return response()->json(['status' => 'sucesso!', 'novaQuantidade' => $recurso->quantidadeRecurso], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Método para diminuir a quantidade de um recurso.
     */
    public function diminuirQuantidade(string $id)
    {
        try {
            $recurso = Recursos::findOrFail($id);
    
            if ($recurso->quantidadeRecurso <= 0) {
                throw new Exception("Não é possível diminuir a quantidade, pois ela já é zero.");
            }
    
            // Diminui a quantidade do recurso em 1
            $recurso->quantidadeRecurso -= 1;
            $recurso->save();
    
            return response()->json(['status' => 'sucesso!', 'novaQuantidade' => $recurso->quantidadeRecurso], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
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
