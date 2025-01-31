<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailValidation extends Mailable
{
    use Queueable, SerializesModels;

    public $validationUrl; // URL de validation
    public $user; // Utilisateur

    /**
     * Create a new message instance.
     */
    public function __construct($user, $validationUrl)
    {
        $this->user = $user;
        $this->validationUrl = $validationUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Confirmation Email')
                    ->view('emails.validation')
                    ->with([
                        'user' => $this->user,
                        'validationUrl' => $this->validationUrl,
                    ]);
    }
}
