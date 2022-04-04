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
        $contenido = [
            'name'=>$request->name,
            'content'=>$request->content,
            'file'=>$request->file('file'),
            'content2'=>$request->content2,
            'file2'=>$request->file('file2')
        ];
        $correo = new PersonaFisica($contenido);
        Mail::to('gaston.estevez7@gmail.com')->send($correo);
        return 'mail enviado';
    }
}
