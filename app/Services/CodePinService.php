<?php

namespace App\Services;

use App\Mail\EmailCodePin;
use App\Models\CodePin;
use App\Models\Utilisateur;
use App\Repositories\CodePinRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CodePinService
{
    private $codePin;

    public function __construct()
    {
        $this->codePin = new CodePin();
    }

    public function envoyerCodePin(Utilisateur $utilisateur): int
    {
        $code = random_int(100000, 999999);  
        $expiration = Carbon::now()->addSeconds(90);  

        
        while (CodePin::where('codepin', operator: $code)->exists()) {
            $code = random_int(100000, 999999);  
        }

         
        $codePin = new CodePin();
        $codePin->codepin = $code;
        $codePin->date_expiration = $expiration;
        $codePin->id_utilisateurs = $utilisateur->id_utilisateurs;
        $codePin->save();

         
        try {
            $this->envoyerEmailConfirmation($utilisateur, code: $code);
            return $code;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
            return 0;
        }
    }

     
    public function verifierCodePin(int $codepin, int $utilisateurId): bool
    {
        $codePin = CodePin::where('codepin', $codepin)
                          ->where('utilisateur_id', $utilisateurId)
                          ->first();

        if ($codePin && $codePin->date_expiration->isFuture()) {
             
            $codePin->delete();
            return true;
        }

        return false;
    }

     
    private function envoyerEmailConfirmation(Utilisateur $utilisateur, int $code)
    {
        Mail::to($utilisateur->email)->send(new EmailCodePin($utilisateur, $code));
    }
}
