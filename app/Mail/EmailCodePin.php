<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailCodePin extends Mailable
{
    use Queueable, SerializesModels;

    public $prenom; 
    public $nom;  
    public $code;  

    /**
     * Crée une nouvelle instance du message.
     *
     * @param  \App\Models\Utilisateur  $utilisateur
     * @param  int  $code
     * @return void
     */
    public function __construct($utilisateur, $code)
    {
        $this->prenom = $utilisateur->prenom;
        $this->nom = $utilisateur->nom;
        $this->code = $code;
    }

    /**
     * Construire le message de l'email.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Code PIN de Confirmation')
                    ->view('emails.code_pin') // Utilise la vue 'emails.code_pin'
                    ->with([
                        'prenom' => $this->prenom,
                        'nom' => $this->nom,
                        'code' => $this->code,
                    ]);
    }
}
