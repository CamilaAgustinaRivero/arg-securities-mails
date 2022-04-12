<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PersonaFisica;

class DocumentacionFisicasController extends Controller
{
    public function index() {
        return view('emails.clientTemplate');

    }

    private function sendSuccessPersonaFisica(Request $request) {
        $contenido = [
            'name'=> $request->nombre,
            'cuit' => $request->cuit,
            // 'dniFrenteDorso'=>$request->file('dniFrenteDorso'),
            // 'constanciaOrigenDeFondos'=>$request->file('constanciaOrigenDeFondos'),
        ];
        $correo = new PersonaFisica($contenido);
        Mail::to('gaston.estevez7@gmail.com')->send($correo);
        
    }

    private function sendClientTemplate(Request $request) {
        $contenido = [
            'name'=> $request->nombre,
            'cuit' => $request->cuit,
            'email' => $request->email,
        ];
        $correo = new PersonaFisica($contenido);
        Mail::to($request->email)->send($correo);
    }

    public function store(Request $request) {
        try {
            // $this->sendSuccessPersonaFisica($request);
            $this->sendClientTemplate($request);
            return response()->json([
                'message' => 'Successfully sent emails!',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

        return 'mail enviado';
    }
}
