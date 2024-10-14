<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Eventos;

class EventosController extends Controller
{
    
    public function index(Request $request)
    {
        return Eventos::where('idCliente', $request->idCliente)->with(['local'])->get();
    }

    public function listandoProximosEventos(Request $request)
    {
        $hoje = now(); // Obtém a data atual

        return Eventos::where('idCliente', $request->idCliente)
            ->where('dataEvento', '>=', $hoje) // Filtra eventos com data igual ou posterior à data atual
            ->with(['local']) // Carrega a relação com 'local'
            ->orderBy('dataEvento') // Ordena os eventos pela data
            ->take(3) // Limita a 5 eventos
            ->get();
    }

    
    public function store(Request $request)
    {
        try{

            $evento = Eventos::create([
                "nomeEvento" => $request->nomeEvento,
                "descricaoEvento" => $request->descricaoEvento,
                "dataEvento" => $request->dataEvento,
                "localEvento" => $request->localEvento,
                "prioridadeEvento" => $request->prioridadeEvento,
                "orcamentoEvento" => $request->orcamentoEvento,
                "idCliente" => $request->idCliente
            ]);

        } catch(Exception $e){
            return response()->json(['error' => 'Ocorreu um erro ao salvar o evento.'], 500);
        }

        return response()->json(['message' => 'O evento foi salvo com sucesso!', 'evento' => $evento], 201);
    }

    
    public function show(string $id)
    {
        try{

            if(!$id){
                return response()->json(['error' => 'ID do evento não informado.'], 400);
            }

            $evento = Eventos::find($id);

            if(!$evento){
                return response()->json(['error' => 'Evento não encontrado.'], 404);
            }

        } catch(Exception $e){
            return response()->json(['error' => 'Ocorreu um erro ao buscar o evento.'], 500);
        }

        return response()->json(['evento' => $evento], 200);
    }

    
    public function update(Request $request, string $id)
    {
        try{

            $evento = Eventos::find($id);

            if(!$evento){
                throw new Exception("Evento não encontrado!");
            }

            $evento->nomeEvento = $request->nomeEvento ?? $evento->nomeEvento;
            $evento->descricaoEvento = $request->descricaoEvento ?? $evento->descricaoEvento;
            $evento->localEvento = $request->localEvento ?? $evento->localEvento;
            $evento->dataEvento = $request->dataEvento ?? $evento->dataEvento;
            $evento->prioridadeEvento = $request->prioridadeEvento ?? $evento->prioridadeEvento;
            $evento->orcamentoEvento = $request->orcamentoEvento ?? $evento->orcamentoEvento;

            $evento->save();

        } catch(Exception $e){
            return response()->json(['message' => "Erro ao editar evento!", 'error' => $e->getMessage()]);
        }

        return response()->json(['message' => "Evento editado com sucesso!", 'evento' => $evento]);
    }

    public function gerarRelatorioEventos(Request $request)
{
    $idCliente = $request->idCliente;

    $eventos = Eventos::where('idCliente', $idCliente)->get();

    // Contar a quantidade de eventos para o cliente
    $quantidadeEventos = Eventos::where('idCliente', $idCliente)->count();

    // Obter o evento mais caro de acordo com a coluna 'orcamentoEvento'
    $eventoMaisCaro = Eventos::where('idCliente', $idCliente)
        ->orderBy('orcamentoEvento', 'desc')
        ->first();

    // Obter o ano atual
    $anoAtual = date('Y');

    // Contar a quantidade de eventos por mês no ano atual
    $eventosPorMes = Eventos::where('idCliente', $idCliente)
        ->whereYear('dataEvento', $anoAtual)
        ->selectRaw('MONTH(dataEvento) as mes, COUNT(*) as total')
        ->groupBy('mes')
        ->orderBy('total', 'desc')
        ->get();

    // Identificar o mês com mais eventos
    $mesComMaisEventos = $eventosPorMes->first(); // O primeiro será o mês com mais eventos

    // Nome dos meses para exibição
    $nomeMeses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];

    return response()->json([
        'eventos' => $eventos,
        'quantidadeEventos' => $quantidadeEventos,
        'eventoMaisCaro' => $eventoMaisCaro ? [
            'titulo' => $eventoMaisCaro->nomeEvento,
            'orcamento' => $eventoMaisCaro->orcamentoEvento,
            'data' => $eventoMaisCaro->dataEvento
        ] : null,
        'mesComMaisEventos' => $mesComMaisEventos ? [
            'mes' => $nomeMeses[$mesComMaisEventos->mes],
            'totalEventos' => $mesComMaisEventos->total
        ] : null
    ]);
}

    

    public function destroy(string $id)
    {
        //
    }
}
