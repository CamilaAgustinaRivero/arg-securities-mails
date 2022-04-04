<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PersonaFisica extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = "PERSONA FÍSICA | Envío de documentación";
    public $contenido;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($contenido)
    {
        $this->contenido = $contenido;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.personaFisica')
        ->attach($this->contenido['file']->getRealPath(), [
            'as'=>$this->contenido['file']->getClientOriginalName()
        ])
        ->attach($this->contenido['file2']->getRealPath(), [
            'as'=>$this->contenido['file2']->getClientOriginalName()
        ]);;
    }
}
