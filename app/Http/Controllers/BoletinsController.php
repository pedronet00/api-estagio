<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Boletins;
use Exception;

class BoletinsController extends Controller
{
    
    public function index()
    {
        return Boletins::all();
    }

    public function store(Request $request)
    {
        try{

            if(!$request->dataCulto){
                throw new Exception("A data do culto deve ser preenchida!");
            }

            if($request->dataCulto < date('Y-m-d')){
                throw new Exception("A data do culto não pode ser anterior a data de hoje!");
            }

            if(!$request->turnoCulto){
                throw new Exception("O turno do culto deve ser preenchido!");
            }

            if(!$request->transmissaoCulto){
                throw new Exception("A transmissão do culto deve ser preenchida!");
            }

            if(!$request->filmagemCulto){
                throw new Exception("A filmagem do culto deve ser preenchida!");
            }

            if(!$request->fotoCulto){
                throw new Exception("A foto do culto deve ser preenchida!");
            }

            if(!$request->apoioCulto){
                throw new Exception("O apoio do culto deve ser preenchido!");
            }

            if(!$request->regenciaCulto){
                throw new Exception("A regência do culto deve ser preenchida!");
            }

            if(!$request->pianoCulto){
                throw new Exception("O piano do culto deve ser preenchido!");
            }

            if(!$request->orgaoCulto){
                throw new Exception("O órgão do culto deve ser preenchido!");
            }

            if(!$request->somCulto){
                throw new Exception("O som do culto deve ser preenchido!");
            }

            if(!$request->micVolanteCulto){
                throw new Exception("O microfone volante do culto deve ser preenchido!");
            }

            if(!$request->apoioInternetCulto){
                throw new Exception("O apoio à internet do culto deve ser preenchido!");
            }

            if(!$request->cultoInfantilCulto){
                throw new Exception("O culto infantil do culto deve ser preenchido!");
            }

            if(!$request->bercarioCulto){
                throw new Exception("O berçário do culto deve ser preenchido!");
            }

            if(!$request->recepcaoCulto){
                throw new Exception("A recepção do culto deve ser preenchida!");
            }

            if(!$request->aconselhamentoCulto){
                throw new Exception("O aconselhamento do culto deve ser preenchido!");
            }

            if(!$request->estacionamentoCulto){
                throw new Exception("O estacionamento do culto deve ser preenchido!");
            }

            if(!$request->diaconosCulto){
                throw new Exception("Os diáconos do culto devem ser preenchidos!");
            }

            $boletim = Boletins::create([
                'dataCulto' => $request->dataCulto,
                'turnoCulto' => $request->turnoCulto,
                'transmissaoCulto' => $request->transmissaoCulto,
                'filmagemCulto' => $request->filmagemCulto,
                'fotoCulto' => $request->fotoCulto,
                'apoioCulto' => $request->apoioCulto,
                'regenciaCulto' => $request->regenciaCulto,
                'pianoCulto' => $request->pianoCulto,
                'orgaoCulto' => $request->orgaoCulto,
                'somCulto' => $request->somCulto,
                'micVolanteCulto' => $request->micVolanteCulto,
                'apoioInternetCulto' => $request->apoioInternetCulto,
                'cultoInfantilCulto' => $request->cultoInfantilCulto,
                'bercarioCulto' => $request->bercarioCulto,
                'recepcaoCulto' => $request->recepcaoCulto,
                'aconselhamentoCulto' => $request->aconselhamentoCulto,
                'estacionamentoCulto' => $request->estacionamentoCulto,
                'diaconosCulto' => $request->diaconosCulto,
            ]);
        } catch(Exception $e){
            return response()->json([
                'error' => 'Ocorreu um erro ao tentar salvar o boletim.'
            ], 500);
        }

        return response()->json(['message'=> 'Boletim cadastrado com sucesso!', 'boletim' => $boletim], 201);
    }

    public function show(string $id)
    {
        try{

            if(!$id){
                throw new Exception("ID do boletim não fornecido!");
            }

            $boletim = Boletins::find($id);

            if(!$boletim){
                throw new Exception("Boletim não encontrado!");
            }

        } catch(Exception $e){
            return response()->json(['error' => $e->getMessage]);
        }

        return response()->json(['success' => $boletim]);
    }

    public function update(Request $request, string $id)
    {
        try{

            if(!$id){
                throw new Exception("ID do boletim não fornecido!");
            }

            $boletim = Boletins::find($id);
            
            if(!$boletim){
                throw new Exception("Boletim não encontrado!");
            }

            $boletim->dataCulto = $request->dataCulto ?? $boletim->dataCulto;
            $boletim->turnoCulto = $request->turnoCulto ?? $boletim->turnoCulto;
            $boletim->transmissaoCulto = $request->transmissaoCulto ?? $boletim->transmissaoCulto;
            $boletim->filmagemCulto = $request->filmagemCulto ?? $boletim->filmagemCulto;
            $boletim->fotoCulto = $request->fotoCulto ?? $boletim->fotoCulto;
            $boletim->apoioCulto = $request->apoioCulto ?? $boletim->apoioCulto;
            $boletim->regenciaCulto = $request->regenciaCulto ?? $boletim->regenciaCulto;
            $boletim->pianoCulto = $request->pianoCulto ?? $boletim->pianoCulto;
            $boletim->orgaoCulto = $request->orgaoCulto ?? $boletim->orgaoCulto;
            $boletim->somCulto = $request->somCulto ?? $boletim->somCulto;
            $boletim->micVolanteCulto = $request->micVolanteCulto ?? $boletim->micVolanteCulto;
            $boletim->apoioInternetCulto = $request->apoioInternetCulto ?? $boletim->apoioInternetCulto;
            $boletim->cultoInfantilCulto = $request->cultoInfantilCulto ?? $boletim->cultoInfantilCulto;
            $boletim->bercarioCulto = $request->bercarioCulto ?? $boletim->bercarioCulto;
            $boletim->recepcaoCulto = $request->recepcaoCulto ?? $boletim->recepcaoCulto;
            $boletim->aconselhamentoCulto = $request->aconselhamentoCulto ?? $boletim->aconselhamentoCulto;
            $boletim->estacionamentoCulto = $request->estacionamentoCulto ?? $boletim->estacionamentoCulto;
            $boletim->diaconosCulto = $request->diaconosCulto ?? $boletim->diaconosCulto;

            $boletim->save();

        } catch(Exception $e){
            return response()->json([
                'error' => 'Ocorreu um erro ao tentar atualizar o boletim.'
            ], 500);
        }

        return response()->json(['message'=> 'Boletim atualizado com sucesso!']);
    }

    public function destroy(string $id)
    {

        try{

            if(!$id){
                throw new Exception("ID do boletim não fornecido!");
            }

            $boletim = Boletins::find($id);

            if(!$boletim){
                throw new Exception("Boletim não encontrado!");
            }

            $boletim->delete();
        } catch(Exception $e){
            return response()->json(['message' => 'Erro: '. $e->getMessage()]);
        }
        

        return response()->json(['message'=> 'Boletim excluído com sucesso!']);
    }

    public function search(Request $request)
    {
        $query = Boletins::query();

        // Filtro por data específica
        if ($request->has('dataCulto')) {
            $query->whereDate('dataCulto', $request->input('dataCulto'));
        }

        // Filtro por intervalo de datas
        if ($request->has('dataInicio') && $request->has('dataFim')) {
            $query->whereBetween('dataCulto', [$request->input('dataInicio'), $request->input('dataFim')]);
        }

        // Filtro por turno
        if ($request->has('turnoCulto')) {
            $query->where('turnoCulto', $request->input('turnoCulto'));
        }

        // Filtro por transmissão
        if ($request->has('transmissaoCulto')) {
            $query->where('transmissaoCulto', $request->input('transmissaoCulto'));
        }

        // Filtro por filmagem
        if ($request->has('filmagemCulto')) {
            $query->where('filmagemCulto', $request->input('filmagemCulto'));
        }

        // Filtro por foto
        if ($request->has('fotoCulto')) {
            $query->where('fotoCulto', $request->input('fotoCulto'));
        }

        // Filtro por apoio
        if ($request->has('apoioCulto')) {
            $query->where('apoioCulto', $request->input('apoioCulto'));
        }

        // Filtro por regência
        if ($request->has('regenciaCulto')) {
            $query->where('regenciaCulto', $request->input('regenciaCulto'));
        }

        // Filtro por piano
        if ($request->has('pianoCulto')) {
            $query->where('pianoCulto', $request->input('pianoCulto'));
        }

        // Filtro por órgão
        if ($request->has('orgaoCulto')) {
            $query->where('orgaoCulto', $request->input('orgaoCulto'));
        }

        // Filtro por som
        if ($request->has('somCulto')) {
            $query->where('somCulto', $request->input('somCulto'));
        }

        // Filtro por microvolante
        if ($request->has('micVolanteCulto')) {
            $query->where('micVolanteCulto', $request->input('micVolanteCulto'));
        }

        // Filtro por apoio à internet
        if ($request->has('apoioInternetCulto')) {
            $query->where('apoioInternetCulto', $request->input('apoioInternetCulto'));
        }

        // Filtro por culto infantil
        if ($request->has('cultoInfantilCulto')) {
            $query->where('cultoInfantilCulto', $request->input('cultoInfantilCulto'));
        }

        // Filtro por bercário
        if ($request->has('bercarioCulto')) {
            $query->where('bercarioCulto', $request->input('bercarioCulto'));
        }

        // Filtro por recepção
        if ($request->has('recepcaoCulto')) {
            $query->where('recepcaoCulto', $request->input('recepcaoCulto'));
        }

        // Filtro por aconselhamento
        if ($request->has('aconselhamentoCulto')) {
            $query->where('aconselhamentoCulto', $request->input('aconselhamentoCulto'));
        }

        // Filtro por estacionamento
        if ($request->has('estacionamentoCulto')) {
            $query->where('estacionamentoCulto', $request->input('estacionamentoCulto'));
        }

        // Filtro por diáconos
        if ($request->has('diaconosCulto')) {
            $query->where('diaconosCulto', $request->input('diaconosCulto'));
        }

        

        $resultados = $query->get();

        return response()->json($resultados);
    }


}
