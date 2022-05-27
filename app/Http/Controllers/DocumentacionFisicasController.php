<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PersonaFisica;
use App\Mail\PersonaJuridica;
use App\Mail\RegisterSuccess;

class DocumentacionFisicasController extends Controller
{
    public function preview()
    {
        return view('emails.clientTemplate');
    }

    private function sendClientTemplate(Request $request)
    {
        $contenido = [
            'name' => $request->nombre,
            'cuit' => $request->cuit,
            'email' => $request->email,
        ];

        if ($request->isPersonaJuridica) {
            $correo = new PersonaJuridica($contenido);
        } else {
            $correo = new PersonaFisica($contenido);
        }
        Mail::to($request->email)->send($correo);
        $register = new RegisterSuccess($request->all());
        Mail::to("aperturas@argsecurities.com")->send($register);
        // Mail::to("gaston.estevez7@gmail.com")->send($register);


    }

    public function store(Request $request)
    {
        try {
            // $this->sendSuccessPersonaFisica($request);
            $this->sendClientTemplate($request);
            return response()->json([
                'message' => 'Successfully sent emails!',
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'message' => $th->getMessage(),
                'object' => $th->getLine(),
            ], 500);
        }

        return 'mail enviado';
    }
}
