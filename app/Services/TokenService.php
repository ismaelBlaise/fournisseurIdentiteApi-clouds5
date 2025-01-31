<?php

namespace App\Services;

use App\Models\Configuration;
use App\Models\Utilisateur;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TokenService
{
    public function __construct()
    {
        
    }

     
    public function creationToken(Utilisateur $utilisateur): Token
    {
        $tokenStr = Str::uuid()->toString();  
        $token = new Token();
        $token->token = $tokenStr;
        $token->id_utilisateurs = $utilisateur->id_utilisateurs;
        $token->date_expiration = now()->addMinutes(1);   
        $token->save();

        return $token;
    }

     
    public function recupererTokenUtilisateur($utilisateurDTO)
    {
        $utilisateur = $utilisateurDTO->toUtilisateur();

         
        $token = Token::where('id_utilisateurs', $utilisateur->id)->first();

        return $token;
    }

     
    public function genererDateExpiration(Token $token): Token
    {
         
        $configuration = Configuration::
             where('cle', 'token_lifetime')
            ->first();

        if (!$configuration) {
            throw new \RuntimeException('Configuration non trouvée pour la durée du token.');
        }

        $token->date_expiration = now()->addSeconds((int) $configuration->valeur);
        $token->save();

        return $token;
    }

     
    public function isTokenValid(string $tokenValue): bool
    {
         
        $token = Token::where('token', $tokenValue)->first();

        if (!$token) {
            throw new \RuntimeException('Token non trouvé.');
        }

        
        if (Carbon::parse($token->date_expiration)->isBefore(Carbon::now())) {
            $token->delete();
            return false;  
        }

        return true;  
    }
}
