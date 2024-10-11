<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Livros;

class LivrosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtenha o idCliente do usuário autenticado
        $idCliente = $request->idCliente; 

        // Busque os livros do cliente autenticado
        $livros = Livros::where('idCliente', $idCliente)->get();

        // Retorne os livros em formato JSON
        return response()->json($livros);
    }

    public function download($id)
    {
        $livro = Livros::findOrFail($id);
        $pathToFile = storage_path("app/public/livros/{$livro->idCliente}_{$livro->nomeLivro}.pdf");

        return response()->download($pathToFile);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'nomeLivro' => 'required|string|max:255',
            'autorLivro' => 'required|string|max:255',
            'urlLivro' => 'required|file|mimes:pdf|max:2048', // Permitindo apenas PDFs
        ]);

        // Obter o ID do cliente autenticado
        $idCliente = $request->idCliente;

        // Armazenar o arquivo PDF na pasta 'livros' com prefixo
        $file = $request->file('urlLivro');
        $fileName = $idCliente . '_' . $file->getClientOriginalName(); // Prefixo do cliente
        $filePath = $file->storeAs('livros', $fileName, 'public'); // Salva no disco 'public'

        // Criar o livro no banco de dados
        $livro = Livros::create([
            'nomeLivro' => $request->nomeLivro,
            'autorLivro' => $request->autorLivro,
            'urlLivro' => $fileName, // Nome do arquivo para armazenar no banco
            'idCliente' => $idCliente,
        ]);

        return response()->json($livro, 201);
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
