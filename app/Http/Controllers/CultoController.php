<?php

namespace App\Http\Controllers;

use App\Models\Culto;
use App\Models\EscalasCultos;
use App\Models\FuncoesCulto;
use Illuminate\Http\Request;

class CultoController extends Controller
{
    public function index(Request $request)
    {
        return Culto::where('idCliente', $request->idCliente)->with('local')->orderBy('dataCulto', 'asc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dataCulto' => 'required|date',
            'turnoCulto' => 'required|integer',
            'idCliente' => 'required|integer',
            'localCulto' => 'required|integer|exists:locais,id'
        ]);

        $culto_repetido = Culto::where('dataCulto', $request->dataCulto)
        ->where('turnoCulto', $request->turnoCulto)
        ->where('localCulto', $request->localCulto)
        ->where('idCliente', $request->idCliente)
        ->exists();
        if($culto_repetido){
            return response()->json(['error' => 'Erro: já existe um culto nesta data, turno e local.'], 422);        
        }

        return Culto::create($validated);
    }

    public function show($id)
    {
        return Culto::findOrFail($id);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'dataCulto' => 'required|date',
            'turnoCulto' => 'required|integer',
            'localCulto' => 'required|integer|exists:locais,id'
        ]);

        $culto_repetido = Culto::where('dataCulto', $request->dataCulto)
        ->where('turnoCulto', $request->turnoCulto)
        ->where('localCulto', $request->localCulto)
        ->where('idCliente', $request->idCliente)
        ->exists();
        
        if($culto_repetido){
            return response()->json(['error' => 'Erro: já existe um culto nesta data, turno e local.'], 422);        
        }

        $culto = Culto::findOrFail($request->idCulto);
        $culto->update($validated);

        return $culto;
    }

    public function destroy(Request $request)
    {
        $culto = Culto::findOrFail($request->id);
        $culto->delete();

        return response()->json(['message' => 'Culto deletado com sucesso']);
    }

    public function cultoReport(Request $request)
    {
        $dataInicial = $request->dataInicial . ' 00:00:00';
        $dataFinal = $request->dataFinal . ' 23:59:59';

        $cultos = Culto::where('idCliente', $request->idCliente)
        ->whereBetween('dataCulto', [$dataInicial, $dataFinal])
        ->get();
        
        
        $quantidade_cultos = Culto::where('idCliente', $request->idCliente)
        ->whereBetween('dataCulto', [$dataInicial, $dataFinal])
        ->count();

        $quantidade_cultos_manha = Culto::where('idCliente', $request->idCliente)
        ->whereBetween('dataCulto', [$dataInicial, $dataFinal])
        ->where('turnoCulto', 0)
        ->count();

        $quantidade_cultos_noite = Culto::where('idCliente', $request->idCliente)
        ->whereBetween('dataCulto', [$dataInicial, $dataFinal])
        ->where('turnoCulto', 1)
        ->count();

        return response()->json([
            'qtdeCultos' => $quantidade_cultos,
            'qtdeCultosManha' =>$quantidade_cultos_manha,
            'qtdeCultosNoite' =>$quantidade_cultos_noite,
            'cultos' => $cultos
        ]);

    }

}