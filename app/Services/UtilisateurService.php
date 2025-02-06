<?php

namespace App\Services;

use App\Models\Utilisateur;
use App\Models\Token;
use App\Models\CodePin;
use App\Models\Configuration;
use App\DataTransferObjects\ConnexionDTO;
use App\DataTransferObjects\SexeDTO;
use App\DataTransferObjects\UtilisateurCreateDTO;
use App\DataTransferObjects\UtilisateurDTO;
use App\DataTransferObjects\UtilisateurUpdateDTO;
use App\DataTransferObjects\ValidationEmailDTO;
use App\DataTransferObjects\ValidationPinDTO;
use App\Mail\EmailReinitialisation;
use App\Models\Sexe;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailValidation;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\URL;

class UtilisateurService
{
    private $tokenService;

    private $codePinService;

    public function __construct(TokenService $tokenService,CodePinService $code)
    {
        $this->tokenService = $tokenService;  
        $this->codePinService= $code;
    }

    public function save(UtilisateurDTO $data)
    {
        $utilisateur = $data->toUtilisateur();
        $utilisateur->save();
        return $data;
    }

    

    public function update($id, UtilisateurUpdateDTO $data)
    {
         
        $utilisateur = Utilisateur::findOrFail($id);

         
        $utilisateur->nom = $data->getNom();
        $utilisateur->prenom = $data->getPrenom();
        $utilisateur->date_naissance = $data->getDateNaissance();

         
        if (!empty($data->getMotDePasse())) {
            $utilisateur->mot_de_passe = Hash::make($data->getMotDePasse());
        }

        
        $sexe = Sexe::find($data->getIdSexe());
        if (!$sexe) {
            throw new \InvalidArgumentException("Le sexe avec l'ID {$data->getIdSexe()} n'existe pas.");
        }
        $utilisateur->sexe()->associate($sexe);

        
        $utilisateur->save();

         
        return new UtilisateurDTO(
            $utilisateur->id_utilisateurs,
            $utilisateur->email,
            $utilisateur->nom,
            $utilisateur->prenom,
            Carbon::parse($utilisateur->date_naissance),
            $utilisateur->mot_de_passe,
            $utilisateur->etat,
            $utilisateur->nb_tentative,
            new SexeDTO($sexe->id_sexe, $sexe->sexe)
        );
    }



    public function demanderReinitialisation(UtilisateurDTO $dto)
    {
        $utilisateur =  Utilisateur::where("email",$dto->getEmail())->first();
        if (!$utilisateur) {
            throw new Exception("Utilisateur non trouvé");
        }
        
        $newToken = $this->tokenService->creationToken($utilisateur);
        $url=$this->creationUrlReinitialisation($newToken);
        $this->emailReinitialisation($dto,$url);
        return $url;
    }


    public function creationUrlValidation(Token $token): string
    {
        $validationUrl = URL::to('/api/utilisateurs/valider-compte') . '?' . http_build_query(['token' => $token->token]);
        return $validationUrl;
    }


    public function creationUrlReinitialisation(Token $token): string
    {
        $reinitialisationUrl = URL::to('/api/utilisateurs/reinitialiser-tentative') . '?' . http_build_query(['token' => $token->token]);
        return $reinitialisationUrl;
    }

    
    
    public function inscrireUtilisateur(UtilisateurDTO $dto)
    {
        
        $utilisateur = Utilisateur::where('email', $dto->getEmail())->first();

        if ($utilisateur) {
            
            if (!$utilisateur->etat) {
                $token = $this->tokenService->recupererTokenUtilisateur($utilisateur);

                 
                if (Carbon::parse($token->date_expiration)->isPast()) {
                     
                    $newToken = $this->tokenService->creationToken($utilisateur);
                    $newUrl = $this->creationUrlValidation($newToken);

                     
                    $this->emailValidation($dto, $newUrl);

                    return $newUrl;
                }

                 
                $url = $this->creationUrlValidation($token);
                $this->emailValidation($dto, $url);

                return $url;
            }

             
            throw new Exception("L'adresse email est déjà utilisée.");
        }

         
        $dto->setMotDePasse(Hash::make($dto->getMotDePasse()));
        $dto->setEtat(false);

        $utilisateur = $dto->toUtilisateur();  
        $utilisateur->save();  

         
        $token = $this->tokenService->creationToken($utilisateur);

        
        $url = $this->creationUrlValidation($token);

         
        $this->emailValidation($dto, $url);

        return $url;
    }

    

    public function validerCompte($tokenStr)
    {
        $token = Token::where("token",$tokenStr)->first();
        if ( Carbon::parse($token->date_expiration)->isBefore(Carbon::now()) || !$token) {
            throw new Exception("Token invalide ou expiré");
        }

        $utilisateur = $token->utilisateur;
        $utilisateur->etat = true;
        $utilisateur->nb_tentative = $this->getMaxAttempts();
        $utilisateur->save();
        $token->delete();
        return $utilisateur->id_utilisateurs;
    }

    public function reinitialiserTentative($tokenStr)
    {
        $token = Token::where("token",$tokenStr)->first();
        $utilisateur = $token->utilisateur;
        $utilisateur->nb_tentative = $this->getMaxAttempts();
        $utilisateur->save();
        $token->delete();
    }

    public function connexion($email, $mdp)
    {
        $utilisateur = Utilisateur::where('email', $email)->first();
        if (!$utilisateur) {
            throw new Exception("L'adresse email est introuvable");
        }

        if (!$utilisateur->etat) {
            throw new Exception("Votre compte n'est pas encore validé");
        }

        if ($utilisateur->nb_tentative == 0) {
            return "0x0:" . $this->demanderReinitialisation(new UtilisateurDTO(
                $utilisateur->id_utilisateurs,
                $utilisateur->email,
                $utilisateur->nom,
                $utilisateur->prenom,
                Carbon::parse($utilisateur->date_naissance),
                $utilisateur->mot_de_passe,
                $utilisateur->etat,
                $utilisateur->nb_tentative,
                new SexeDTO($utilisateur->sexe->id_sexe, $utilisateur->sexe->sexe)
            ));
        }

        if (!Hash::check($mdp, $utilisateur->mot_de_passe)) {
            $this->incrementNbTentative($utilisateur);
            throw new Exception("Mot de passe incorrect");
        }

        return $email;
    }

    public function emailValidation(UtilisateurDTO $dto, $validationUrl)
    {
        $email = $dto->getEmail();

        try {
            Mail::to($email)->send(new EmailValidation($dto, $validationUrl));
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de l'envoi de l'email de validation : " . $e->getMessage(), 0, $e);
        }
    }

    public function emailReinitialisation(UtilisateurDTO $dto, $validationUrl)
    {
        $email = $dto->getEmail();

        try {
            Mail::to($email)->send(new EmailReinitialisation($dto, $validationUrl));
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de l'envoi de l'email de validation : " . $e->getMessage(), 0, $e);
        }
    }

    public function validationPin(ValidationPinDTO $validationPinDTO)
    {
        $utilisateur = Utilisateur::where('email', operator: $validationPinDTO->getEmail())->first();
        $codePinEntity=CodePin::where('codepin',$validationPinDTO->getCodePin())->first();

        if ($utilisateur->nb_tentative == 0) {
            return "0x0:" . $this->demanderReinitialisation(new UtilisateurDTO(
                $utilisateur->id,
                $utilisateur->email,
                $utilisateur->nom,
                $utilisateur->prenom,
                Carbon::parse($utilisateur->date_naissance),
                $utilisateur->mot_de_passe,
                $utilisateur->etat,
                $utilisateur->nb_tentative,
                new SexeDTO($utilisateur->sexe->id_sexe, $utilisateur->sexe->sexe)
            ));
        }

        if ($codePinEntity && !Carbon::parse($codePinEntity->date_expiration)->isBefore(Carbon::now())) {
            $this->resetNbTentative($utilisateur);
            $token = $this->tokenService->creationToken($utilisateur);
            $codePinEntity->delete();
            return $token;
        }

        $this->incrementNbTentative($utilisateur);
        return null;
    }

    public function getMaxAttempts()
    {
        $conf = Configuration::where('cle','nbtentative')->first();
        return (int)$conf->valeur;
    }

    protected function incrementNbTentative($utilisateur)
    {
        $utilisateur->nb_tentative -= 1;
        $utilisateur->save();
    }

    protected function resetNbTentative($utilisateur)
    {
        $utilisateur->nb_tentative = $this->getMaxAttempts();
        $utilisateur->save();
    }

    

    
}
