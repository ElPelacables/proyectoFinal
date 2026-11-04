<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        // CA-03.3: Enlace con token seguro
        $url = url('/restablecer?token=' . $this->token); 
        
        return $this->subject('Recuperación de Contraseña')
                    ->html('<p>Para restablecer tu contraseña, haz clic en el siguiente enlace:</p><a href="'.$url.'">Restablecer Contraseña</a><p>Este enlace expira en 30 minutos.</p>');
    }
}