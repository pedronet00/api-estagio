<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FuncoesCulto;

class FuncoesCultoController extends Controller
{
    public function index()
    {
        return FuncoesCulto::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomeFuncao' => 'required|string|max:255',
            'idCliente' => 'required|integer',
        ]);

        return FuncoesCulto::create($validated);
    }

    public function show($id)
    {
        return FuncoesCulto::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nomeFuncao' => 'required|string|max:255',
        ]);

        $funcao = FuncoesCulto::findOrFail($id);
        $funcao->update($validated);

        return $funcao;
    }

    public function destroy($id)
    {
        $funcao = FuncoesCulto::findOrFail($id);
        $funcao->delete();

        return response()->json(['message' => 'Função deletada com sucesso']);
    }
}

