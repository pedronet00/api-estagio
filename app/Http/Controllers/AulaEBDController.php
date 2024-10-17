<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AulaEBD;
use App\Models\ClassesEBD;
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

    public function gerarRelatorioEBD(Request $request){

        $idCliente = $request->idCliente;

        try{
            $qtdeAulas = AulaEBD::where('idCliente', $idCliente)->count();

            if($qtdeAulas == 0){
                throw new Exception("Não existe nenhuma aula cadastrada. Você não pode gerar um relatório enquanto não cadastrar uma aula.");
            }

            $qtdeClasses = ClassesEBD::where('idCliente', $idCliente)->count();
            $soma_presentes = AulaEBD::where('idCliente', $idCliente)->sum('quantidadePresentes');
            $mediaAlunos = $soma_presentes / $qtdeAulas;

            $professorMaisFrequente = AulaEBD::select('professorAula', \DB::raw('count(*) as total'))
            ->where('idCliente', $request->idCliente)
            ->groupBy('professorAula')
            ->with('professor')
            ->orderBy('total', 'desc')
            ->first();

            $aulas = AulaEBD::where('idCliente', $idCliente)->with('classe', 'professor')->get();

        } catch(Exception $e){
            return response()->json([
                'message' => 'Erro!',
                'error' => $e->getMessage()
            ]);
        }

        return response()->json([
            'message' => 'Sucesso!',
            'qtdeAulas' => $qtdeAulas,
            'qtdeClasses' => $qtdeClasses,
            'mediaAlunos' => $mediaAlunos,
            'professorMaisFrequente' => $professorMaisFrequente,
            'aulas' => $aulas
        ]);
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
