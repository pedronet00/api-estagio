<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Eventos;
use Illuminate\Support\Facades\Validator;
use Exception;

class EventosController extends Controller
{
    public function index(Request $request)
    {
        // Validação para garantir que o idCliente esteja presente e seja um inteiro
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        return Eventos::where('idCliente', $request->idCliente)
            ->with(['local'])
            ->get();
    }

    public function listandoProximosEventos(Request $request)
    {
        // Validação para garantir que o idCliente esteja presente e seja um inteiro
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

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
        try {
            // Validação dos dados do evento
            $validator = Validator::make($request->all(), [
                'nomeEvento' => 'required|string|max:255',
                'descricaoEvento' => 'nullable|string|max:500',
                'dataEvento' => 'required|date',
                'localEvento' => 'required|string|max:255',
                'prioridadeEvento' => 'nullable|integer',
                'orcamentoEvento' => 'nullable|numeric',
                'idCliente' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Validando se já existe um evento no mesmo local e data
            $eventoExistente = Eventos::where('localEvento', $request->localEvento)
                ->where('dataEvento', $request->dataEvento)
                ->first();

            // Se já existir, lançar uma exceção
            if ($eventoExistente) {
                return response()->json(['error' => 'O local já está ocupado nesta data.'], 400);
            }

            // Se não existir, prosseguir para criar o novo evento
            $evento = Eventos::create([
                "nomeEvento" => $request->nomeEvento,
                "descricaoEvento" => $request->descricaoEvento,
                "dataEvento" => $request->dataEvento,
                "localEvento" => $request->localEvento,
                "prioridadeEvento" => $request->prioridadeEvento,
                "orcamentoEvento" => $request->orcamentoEvento,
                "idCliente" => $request->idCliente
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => 'Ocorreu um erro ao salvar o evento.'], 500);
        }

        return response()->json(['message' => 'O evento foi salvo com sucesso!', 'evento' => $evento], 201);
    }

    public function show(string $id)
    {
        try {
            if (!$id) {
                return response()->json(['error' => 'ID do evento não informado.'], 400);
            }

            $evento = Eventos::find($id);

            if (!$evento) {
                return response()->json(['error' => 'Evento não encontrado.'], 404);
            }

        } catch (Exception $e) {
            return response()->json(['error' => 'Ocorreu um erro ao buscar o evento.'], 500);
        }

        return response()->json(['evento' => $evento], 200);
    }

    public function update(Request $request, string $id)
    {
        try {
            // Validação dos dados do evento
            $validator = Validator::make($request->all(), [
                'nomeEvento' => 'nullable|string|max:255',
                'descricaoEvento' => 'nullable|string|max:500',
                'dataEvento' => 'nullable|date',
                'localEvento' => 'nullable|string|max:255',
                'prioridadeEvento' => 'nullable|integer',
                'orcamentoEvento' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }

            $evento = Eventos::find($id);

            if (!$evento) {
                throw new Exception("Evento não encontrado!");
            }

            $evento->nomeEvento = $request->nomeEvento ?? $evento->nomeEvento;
            $evento->descricaoEvento = $request->descricaoEvento ?? $evento->descricaoEvento;
            $evento->localEvento = $request->localEvento ?? $evento->localEvento;
            $evento->dataEvento = $request->dataEvento ?? $evento->dataEvento;
            $evento->prioridadeEvento = $request->prioridadeEvento ?? $evento->prioridadeEvento;
            $evento->orcamentoEvento = $request->orcamentoEvento ?? $evento->orcamentoEvento;

            $evento->save();

        } catch (Exception $e) {
            return response()->json(['message' => "Erro ao editar evento!", 'error' => $e->getMessage()]);
        }

        return response()->json(['message' => "Evento editado com sucesso!", 'evento' => $evento]);
    }

    public function gerarRelatorioEventos(Request $request)
    {
        // Validação das datas
        $validator = Validator::make($request->all(), [
            'idCliente' => 'required|integer',
            'dataInicial' => 'required|date',
            'dataFinal' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $idCliente = $request->idCliente;
        $dataInicial = $request->dataInicial;
        $dataFinal = $request->dataFinal;

        // Converte as datas para o formato adequado
        $dataInicial = date('Y-m-d', strtotime($dataInicial)); // Garante que a data seja no formato YYYY-MM-DD
        $dataFinal = date('Y-m-d', strtotime($dataFinal));

        // Filtro de eventos entre as datas fornecidas
        $eventos = Eventos::where('idCliente', $idCliente)
            ->whereBetween('dataEvento', [$dataInicial, $dataFinal])
            ->get();

        // Contar a quantidade de eventos
        $quantidadeEventos = $eventos->count();

        // Obter o evento mais caro
        $eventoMaisCaro = $eventos->sortByDesc('orcamentoEvento')->first();

        // Contar a quantidade de eventos por mês
        $eventosPorMes = [];
        foreach ($eventos as $evento) {
            $mes = date('m', strtotime($evento->dataEvento)); // Extrai o mês da data do evento
            if (!isset($eventosPorMes[$mes])) {
                $eventosPorMes[$mes] = 0;
            }
            $eventosPorMes[$mes]++;
        }

        // Identificar o mês com mais eventos
        $mesComMaisEventos = null;
        $maxEventos = 0;
        foreach ($eventosPorMes as $mes => $totalEventos) {
            if ($totalEventos > $maxEventos) {
                $maxEventos = $totalEventos;
                $mesComMaisEventos = $mes;
            }
        }

        // Nome dos meses
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
                'mes' => $nomeMeses[(int)$mesComMaisEventos],
                'totalEventos' => $maxEventos
            ] : null
        ]);
    }

    public function destroy(string $id)
    {
        $evento = Eventos::findOrFail($id);

        $evento->delete();

        return response()->json([
            'message' => "Evento excluído com sucesso!"
        ]);
    }
}
