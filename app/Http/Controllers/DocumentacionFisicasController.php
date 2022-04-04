<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PersonaFisica;

class DocumentacionFisicasController extends Controller
{
    public function index() {
        return view('documentacionFisicas.index');

    }

    public function store(Request $request) {
        try {
            // throw new \Exception('Error Processing Request', 1);
            $contenido = [
                'name'=> $request->nombre,
                'cuit' => $request->cuit,
                'dniFrenteDorso'=>$request->file('dniFrenteDorso'),
                'constanciaOrigenDeFondos'=>$request->file('constanciaOrigenDeFondos'),
            ];
            $correo = new PersonaFisica($contenido);
            Mail::to('gaston.estevez7@gmail.com')->send($correo);
            return response()->json([
                'message' => 'Successfully access to api!',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

        return 'mail enviado';
    }
}
