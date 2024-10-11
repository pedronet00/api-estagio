<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AulaEBD;
use Exception;

class AulaEBDController extends Controller
{
    
    public function index(Request $request)
    {
        return AulaEBD::where('idCliente', $request->idCliente)->with(['classe', 'professor'])->get();
    }

    
    public function store(Request $request)
    {
        try{

            $aula = AulaEBD::create([
                'dataAula' => $request->dataAula,
                'classeAula' => $request->classeAula,
                'professorAula' => $request->professorAula,
                'quantidadePresentes' => $request->quantidadePresentes,
                'numeroAula' => $request->numeroAula,
                'idCliente' => $request->idCliente
            ]);

        } catch(Exception $e){
            return response()->json(['message' => 'Erro ao salvar aula da EBD!', 'error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Aula cadastrada com sucesso!', 'aula' => $aula], 200);
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
