<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailReinitialisation extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl; 
    public $user;  

     
    public function __construct($user, $resetUrl)
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
    }



    public function build()
    {
        return $this->subject('Réinitialisation du Nombre de Tentatives')
                    ->view('emails.reinitialisation')
                    ->with([
                        'user' => $this->user,
                        'resetUrl' => $this->resetUrl,
                    ]);
    }
 
         
}  
