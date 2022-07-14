<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PersonaJuridica extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = "Muchas gracias por contactarse con ARG Securities Advisors S.A.";
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
        return $this->view('emails.juridicTemplate');
    }
}
