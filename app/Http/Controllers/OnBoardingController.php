<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Recursos;
use App\Models\Departamentos;

class OnBoardingController extends Controller
{
    public function index(Request $request){

        $userCount = User::where('idCliente', $request->idCliente)->count();
        $departamentoCount = Departamentos::where('idCliente', $request->idCliente)->count();
        $recursosCount = Recursos::where('idCliente', $request->idCliente)->count();

        return response()->json([
            'userCount' => $userCount,
            'departamentoCount' => $departamentoCount,
            'recursosCount' => $recursosCount
        ]);
    }
}
