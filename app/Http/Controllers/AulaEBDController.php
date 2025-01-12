<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\AulaEBD;
use App\Models\ClassesEBD;
use Exception;
use Carbon\Carbon;

class AulaEBDController extends Controller
{
    public function index(Request $request)
    {
        return AulaEBD::where('idCliente', $request->idCliente)->with(['classe', 'professor'])->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dataAula' => 'required|date',
            'classeAula' => 'required|integer',
            'professorAula' => 'required|integer',
            'quantidadePresentes' => 'required|integer',
            'numeroAula' => 'required|integer',
            'idCliente' => 'required|integer|exists:clientes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $validator->errors()], 422);
        }

        // Validar se a data da aula é posterior a hoje
        $dataAula = Carbon::parse($request->dataAula);

        if ($dataAula->isFuture()) {
            return response()->json([
                'message' => 'A data da aula não pode ser no futuro!',
            ], 400);
        }

        try {
            // Criar o registro da aula
            $aula = AulaEBD::create($validator->validated());
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao salvar aula da EBD!', 'error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Aula cadastrada com sucesso!', 'aula' => $aula], 200);
    }

    public function gerarRelatorioEBD(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer|exists:clientes,id',
            'dataInicial' => 'required|date',
            'dataFinal' => 'required|date|after_or_equal:dataInicial',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $idCliente = $validated['idCliente'];
        $dataInicial = $validated['dataInicial'];
        $dataFinal = $validated['dataFinal'];

        try {
            $qtdeAulas = AulaEBD::where('idCliente', $idCliente)
                ->whereBetween('dataAula', [$dataInicial, $dataFinal])
                ->count();

            if ($qtdeAulas == 0) {
                throw new Exception("Não existem aulas cadastradas no intervalo especificado.");
            }

            $qtdeClasses = ClassesEBD::where('idCliente', $idCliente)->count();
            $soma_presentes = AulaEBD::where('idCliente', $idCliente)
                ->whereBetween('dataAula', [$dataInicial, $dataFinal])
                ->sum('quantidadePresentes');
            $mediaAlunos = $soma_presentes / $qtdeAulas;

            $professorMaisFrequente = AulaEBD::select('professorAula', \DB::raw('count(*) as total'))
                ->where('idCliente', $idCliente)
                ->whereBetween('dataAula', [$dataInicial, $dataFinal])
                ->groupBy('professorAula')
                ->with('professor')
                ->orderBy('total', 'desc')
                ->first();

            $aulas = AulaEBD::where('idCliente', $idCliente)
                ->whereBetween('dataAula', [$dataInicial, $dataFinal])
                ->with('classe', 'professor')
                ->get();

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro!',
                'error' => $e->getMessage()
            ], 400);
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

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
