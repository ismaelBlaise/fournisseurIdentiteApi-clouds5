<?php

namespace App\DataTransferObjects;

use App\DataTransferObjects\SexeDTO;
use Carbon\Carbon;
use App\Models\Utilisateur;

class UtilisateurDTO
{
    private int $id;
    private string $email;
    private string $nom;
    private string $prenom;
    private Carbon $date_naissance;
    private string $mot_de_passe;
    private bool $etat = false;
    private int $nb_tentative;
    private SexeDTO $sexe;

     
    public function __construct(int $id, string $email, string $nom, string $prenom, Carbon $date_naissance, string $mot_de_passe, bool $etat, int $nb_tentative, SexeDTO $sexe)
    {
        $this->id = $id;
        $this->email = $email;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->date_naissance = $date_naissance;
        $this->mot_de_passe = $mot_de_passe;
        $this->etat = $etat;
        $this->nb_tentative = $nb_tentative;
        $this->sexe = $sexe;
    }

     
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function getDateNaissance(): Carbon
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(Carbon $date_naissance): void
    {
        $this->date_naissance = $date_naissance;
    }

    public function getMotDePasse(): string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): void
    {
        $this->mot_de_passe = $mot_de_passe;
    }

    public function getEtat(): bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): void
    {
        $this->etat = $etat;
    }

    public function getNbTentative(): int
    {
        return $this->nb_tentative;
    }

    public function setNbTentative(int $nb_tentative): void
    {
        $this->nb_tentative = $nb_tentative;
    }

    public function getSexe(): SexeDTO
    {
        return $this->sexe;
    }

    public function setSexe(SexeDTO $sexe): void
    {
        $this->sexe = $sexe;
    }

     
    public function __toString(): string
    {
        return "UtilisateurDTO{id={$this->id}, email='{$this->email}', nom='{$this->nom}', prenom='{$this->prenom}', date_naissance='{$this->date_naissance->toDateString()}', mot_de_passe='{$this->mot_de_passe}', etat={$this->etat}, nb_tentative={$this->nb_tentative}, sexe={$this->sexe}}";
    }

    public function toUtilisateur(): Utilisateur
    {
         
        $utilisateur = new Utilisateur();
        
        $utilisateur->id_utilisateurs = $this->id;
        $utilisateur->email = $this->email;
        $utilisateur->nom = $this->nom;
        $utilisateur->prenom = $this->prenom;
        $utilisateur->date_naissance = $this->date_naissance->toDateString();   
        $utilisateur->mot_de_passe = $this->mot_de_passe;
        $utilisateur->etat = $this->etat;
        $utilisateur->nb_tentative = $this->nb_tentative;
        $utilisateur->id_sexe = $this->sexe->getId();  
        
        return $utilisateur;
    }

}
