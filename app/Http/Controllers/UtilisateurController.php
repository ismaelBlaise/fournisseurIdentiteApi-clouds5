<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use App\Models\Sexe;
use App\Services\CodePinService;
use App\Services\TokenService;
use App\Services\UtilisateurService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\DataTransferObjects\UtilisateurDTO;
use App\DataTransferObjects\UtilisateurUpdateDTO;
use App\DataTransferObjects\SexeDTO;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\DataTransferObjects\ValidationPinDTO;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


/**
 * @OA\Info(title="Utilisateur API", version="1.0")
 * @OA\Server(url="http://localhost/api")
 */
class UtilisateurController extends Controller
{
    protected $utilisateurService;
    protected $codePinService;
    protected $tokenService;
    private $nextId;


    /**
     * UtilisateurController constructor.
     *
     * @param UtilisateurService $utilisateurService
     * @param CodePinService $codePinService
     * @param TokenService $tokenService
     */
    public function __construct(UtilisateurService $utilisateurService, CodePinService $codePinService, TokenService $tokenService)
    {
        $this->utilisateurService = $utilisateurService;
        $this->codePinService = $codePinService;
        $this->tokenService = $tokenService;
        $this->nextId =DB::select("SELECT nextval('utilisateurs_id_utilisateurs_seq') AS nextval")[0]->nextval;
    }

    


    /**
     * Crée un nouvel utilisateur.
     *
     * @OA\Post(
     *     path="/api/utilisateurs",
     *     summary="Créer un utilisateur",
     *     description="Crée un nouvel utilisateur dans le système.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "nom", "prenom", "date_naissance", "mot_de_passe", "etat", "nb_tentative", "id_sexe"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="nom", type="string", example="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Jean"),
     *             @OA\Property(property="date_naissance", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="mot_de_passe", type="string", example="password123"),
     *             @OA\Property(property="etat", type="boolean", example=true),
     *             @OA\Property(property="nb_tentative", type="integer", example=0),
     *             @OA\Property(property="id_sexe", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="nom", type="string", example="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Jean"),
     *             @OA\Property(property="date_naissance", type="string", example="1990-01-01"),
     *             @OA\Property(property="mot_de_passe", type="string", example=""),
     *             @OA\Property(property="etat", type="boolean", example=true),
     *             @OA\Property(property="nb_tentative", type="integer", example=0),
     *             @OA\Property(property="id_sexe", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de validation"
     *     )
     * )
     */
    public function create(Request $request)
    {
        // Validation des données d'entrée
        $data = $request->validate([
            'email' => 'required|email|unique:utilisateurs,email',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'mot_de_passe' => 'required|string|min:8',
            'etat' => 'required|boolean',
            'nb_tentative' => 'required|integer',
            'id_sexe' => 'required|exists:sexes,id_sexe', 
        ], [
            'email.required' => 'L\'email est requis.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'L\'email est déjà utilisé.',
            'nom.required' => 'Le nom est requis.',
            'prenom.required' => 'Le prénom est requis.',
            'date_naissance.required' => 'La date de naissance est requise.',
            'mot_de_passe.required' => 'Le mot de passe est requis.',
            'mot_de_passe.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
            'etat.required' => 'L\'état est requis.',
            'nb_tentative.required' => 'Le nombre de tentatives est requis.',
            'id_sexe.exists' => 'Le sexe sélectionné n\'existe pas.',
        ]);

        try {
             
            $sexeDTO = new SexeDTO($data['id_sexe'], 'NomDuSexe'); 
             
            $utilisateurDTO = new UtilisateurDTO(
                $this->nextId,  
                $data['email'],
                $data['nom'],
                $data['prenom'],
                Carbon::parse($data['date_naissance']),
                Hash::make($data['mot_de_passe']),  
                $data['etat'],
                $this->utilisateurService->getMaxAttempts(),
                $sexeDTO
            );

             
            $sexe = Sexe::find($sexeDTO->getId());
            if (!$sexe) {
                return response()->json([
                    'error' => 'Sexe erreur.',
                    'message' => 'Le sexe avec l\'ID spécifié n\'existe pas'
                ], 404);
            }

            
            $utilisateur = $utilisateurDTO->toUtilisateur();
            
            $utilisateur->sexe()->associate($sexe);
            $utilisateur->save();

            
            $utilisateurDTO->setMotDePasse("");

            return response()->json($utilisateurDTO, 201 );  
        } catch (\Exception $e) {
             
            return response()->json([
                'error' => 'Une erreur est survenue lors de la création de l\'utilisateur.',
                'message' => $e->getMessage()
            ], 500);  
        }
    }


    /**
     * Met à jour un utilisateur existant.
     *
     * @OA\Put(
     *     path="/api/utilisateurs/{id}",
     *     summary="Mettre à jour un utilisateur",
     *     description="Met à jour un utilisateur avec de nouvelles informations.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nom", type="string", example="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Jean"),
     *             @OA\Property(property="date_naissance", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="mot_de_passe", type="string", example="password123"),
     *             @OA\Property(property="id_sexe", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="nom", type="string", example="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Jean"),
     *             @OA\Property(property="date_naissance", type="string", example="1990-01-01"),
     *             @OA\Property(property="mot_de_passe", type="string", example=""),
     *             @OA\Property(property="etat", type="boolean", example=true),
     *             @OA\Property(property="nb_tentative", type="integer", example=0),
     *             @OA\Property(property="id_sexe", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Session expirée"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
         
        $token = str_replace('Bearer ', '', $request->header('Authorization'));

         
        if ($this->tokenService->isTokenValid($token)) {
            
            $data = $request->only(['nom', 'prenom', 'date_naissance', 'mot_de_passe', 'id_sexe']);

             
            $utilisateurUpdateDTO = new UtilisateurUpdateDTO(
                $data['nom'],
                $data['prenom'],
                Carbon::parse($data['date_naissance']),
                $data['mot_de_passe'],
                $data['id_sexe']
            );

             
            $updatedUtilisateur = $this->utilisateurService->update($id, $utilisateurUpdateDTO);

            $updatedUtilisateur->setMotDePasse("");
            return response()->json($updatedUtilisateur, 200);
        } else {
             
            return response()->json([
                'error' => 'Session' ,
                'message' => 'Votre session est expirée'], 403);
        }
    }



    /**
     * Inscrit un utilisateur dans le système.
     *
     * @OA\Post(
     *     path="/api/utilisateurs/inscrire",
     *     summary="Inscrire un utilisateur",
     *     description="Inscrit un utilisateur dans le système en envoyant un lien de validation.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "nom", "prenom", "date_naissance", "mot_de_passe", "sexe"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="nom", type="string", example="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Jean"),
     *             @OA\Property(property="date_naissance", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="mot_de_passe", type="string", example="password123"),
     *             @OA\Property(property="sexe", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur inscrit avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="url", type="string", example="http://example.com/validate")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur de validation"
     *     )
     * )
     */
    public function inscrireUtilisateur(Request $request)
    {    
        try {
             
           
            $validatedData = $request->validate([
                'email' => 'required|email|unique:utilisateurs,email',  
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'date_naissance' => 'required|date|before:today',  
                'mot_de_passe' => 'required|string|min:8', 
                'sexe' => 'required|integer|exists:sexes,id_sexe',  
            ]);
             
            $sexeDTO = new SexeDTO($validatedData['sexe'],"");  

 
            $utilisateurDTO = new UtilisateurDTO(
                $this->nextId,   
                $validatedData['email'],
                $validatedData['nom'],
                $validatedData['prenom'],
                Carbon::parse($validatedData['date_naissance']),
                $validatedData['mot_de_passe'],
                false,   
                0,  
                $sexeDTO
            );

             
            $url = $this->utilisateurService->inscrireUtilisateur($utilisateurDTO);

            
            return response()->json($url, 202);
        } catch (\Illuminate\Validation\ValidationException $e) {
             
            return response()->json([
                'error' => $e->errors(),
                'message' => $e->getMessage()
        ], 422);
        } catch (\Exception $e) {
             
            return response()->json([
                'error' => $e->getMessage(),
                'message' => $e->getMessage()], 400);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/utilisateurs/connexion",
     *     summary="Se connecter à un utilisateur",
     *     description="Vérifie les informations d'identification d'un utilisateur et envoie un code PIN pour la vérification.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "mot_de_passe"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="mot_de_passe", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Code PIN envoyé pour la vérification",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur introuvable ou erreur d'authentification",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Utilisateur introuvable.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Mauvais format ou données manquantes",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Données invalides.")
     *         )
     *     )
     * )
     */
    public function connexion(Request $request)
    {
        try {
            $data = $request->all();
            $utilisateur = Utilisateur::where('email', $data['email'])->first();
            $email = $this->utilisateurService->connexion($data['email'], $data['mot_de_passe']);

                
            if (strpos($email, '0x0:') === 0) {
                return response()->json(['message' => substr($email, 4)], 404);
            }
            $code = $this->codePinService->envoyerCodePin($utilisateur);
            return response()->json([
                'email' => $utilisateur->email,  
                'codepin' => $code                   
            ], 200);
        
        
            
        } catch (\Exception $e) {
            

            return response()->json([
                'error' => $e->getMessage(),
                'message' => $e->getMessage()], 500);       
     }
    }
    

    /**
     * @OA\Get(
     *     path="/api/utilisateurs/valider-compte",
     *     summary="Valider un compte utilisateur",
     *     description="Permet de valider le compte d'un utilisateur en utilisant un token d'activation.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string", example="12345abcd-token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compte validé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Compte validé avec succès !")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur lors de la validation du compte",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Token invalide ou expiré.")
     *         )
     *     )
     * )
     */
    public function validerCompte(Request $request)
    {
        try {
            
            $id_utilisateur = $this->utilisateurService->validerCompte($request->input('token'));

            
            $utilisateur = Utilisateur::where('id_utilisateurs', $id_utilisateur)->first();

            if (!$utilisateur) {
                return response()->json(['error' => 'Utilisateur introuvable.'], 404);
            }

            return view('utilisateur.valider', compact('utilisateur'))
                ->with('message', 'Félicitations, votre compte est maintenant validé !');

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => $e->getMessage()
            ], 400);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/utilisateur",
     *     summary="Récupérer les informations d'un utilisateur par email",
     *     description="Retourne l'id et l'état d'un utilisateur à partir de son email, passé en JSON dans le corps de la requête.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="email", type="string", description="Email de l'utilisateur")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur récupéré avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id_utilisateurs", type="integer"),
     *             @OA\Property(property="etat", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur introuvable",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur interne du serveur",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function recupererUtilisateur(Request $request)
    {
        try {
            
            $email = $request->input('email');
            
            
            $utilisateur = Utilisateur::where('email', $email)->first();

            if (!$utilisateur) {
                return response()->json(['error' => 'Utilisateur introuvable.'], 404);
            }

             
            $utilisateurData = $utilisateur->only(['id_utilisateurs','nb_tentative' ,'etat']);

             
            return response()->json($utilisateurData, 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => $e->getMessage()
            ], 400);
        }
    }




    /**
     * @OA\Post(
     *     path="/api/utilisateurs/reinitialiser-tentative",
     *     summary="Réinitialiser le nombre de tentatives de connexion",
     *     description="Permet de réinitialiser le nombre de tentatives de connexion d'un utilisateur en utilisant un token d'activation.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string", example="12345abcd-token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tentatives réinitialisées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tentatives réinitialisées avec succès !")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur lors de la réinitialisation des tentatives",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Token invalide ou expiré.")
     *         )
     *     )
     * )
     */
    public function reinitialiserTentative(Request $request)
    {
        try {
            $this->utilisateurService->reinitialiserTentative($request->input('token'));
            return response()->json(['message' => 'Tentatives réinitialisées avec succès !'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => $e->getMessage()], 400);
        }
    }

    
    
    /**
     * @OA\Post(
     *     path="/api/utilisateurs/valider-pin",
     *     summary="Valider le code PIN",
     *     description="Permet de valider le code PIN envoyé à l'utilisateur pour l'authentification.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "codepin"},
     *             @OA\Property(property="email", type="string", example="utilisateur@exemple.com"),
     *             @OA\Property(property="codepin", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Validation réussie du code PIN",
     *         @OA\JsonContent(
     *             @OA\Property(property="tokens", type="string", example="newToken12345")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Code PIN non valide ou expiré",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Code PIN non valide ou expiré")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Erreur de validation (erreur interne ou code PIN incorrect)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Code PIN non valide ou expiré")
     *         )
     *     )
     * )
     */
    public function validationPin(Request $request)
    {
        try {
             
            $data = $request->only(['email', 'codepin']);
            
            
            $dto = new ValidationPinDTO(
                $data['email'], 
                $data['codepin']
            );

            
            $tokens = $this->utilisateurService->validationPin($dto);

             
            if ($tokens && strpos($tokens, '0x0:') === 0) {
                return response()->json(['message' => substr($tokens, 4)], 404);
            }
            
            if ($tokens === null) {
                throw new \RuntimeException('Erreur de validation');
            }

            
            return response()->json(['token' => $tokens], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => $e->getMessage()], 400);
        }
    }

}
