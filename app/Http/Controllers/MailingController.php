<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\BirthdayMail;
use App\Mail\DefineUserPasswordMail;
use Exception;

class MailingController extends Controller
{
    public function emailAniversarios()
    {
        $aniversariantes = User::where('dataNascimentoUsuario', date('Y-m-d'))->get();

        if ($aniversariantes) {
            
            foreach ($aniversariantes as $birthday) {
                // Enviar e-mail para cada aniversariante
                Mail::to($birthday['email'])->send(new BirthdayMail($birthday['name']));
            }

            return response()->json(['message' => 'E-mails de aniversário enviados com sucesso.']);
        } else {
            return response()->json(['message' => 'Falha ao obter aniversariantes.'], 500);
        }

    }

    public function emailRedefinirSenha()
    {

        try{
            Mail::to("stabilepedro010403@gmail.com")->send(new DefineUserPasswordMail("12345"));
        } catch(Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
        return response()->json(['message' => 'Email enviado com sucesso!']);
    }
}
