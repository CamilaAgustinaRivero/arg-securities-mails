<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterSuccess extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = "Se ha registrado un nuevo cliente";
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
        return $this->view('emails.juridicTemplate')
        ->attach($this->processPDF(), ['as'=>$this->contenido['dniFrenteDorso']->getClientOriginalName()
        ]);
    }

    private function processPDF() {
        return ['' => ''];
    }
}
