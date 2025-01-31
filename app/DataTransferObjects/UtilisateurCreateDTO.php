<?php

namespace App\DataTransferObjects;

use Carbon\Carbon;

class UtilisateurCreateDTO
{
    private string $email;
    private string $nom;
    private string $prenom;
    private Carbon $date_naissance;
    private string $mot_de_passe;
    private int $id_sexe;

    
    public function __construct(string $email, string $nom, string $prenom, Carbon $date_naissance, string $mot_de_passe, int $id_sexe)
    {
        $this->email = $email;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->date_naissance = $date_naissance;
        $this->mot_de_passe = $mot_de_passe;
        $this->id_sexe = $id_sexe;
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

    public function getIdSexe(): int
    {
        return $this->id_sexe;
    }

    public function setIdSexe(int $id_sexe): void
    {
        $this->id_sexe = $id_sexe;
    }

    
    public function __toString(): string
    {
        return "UtilisateurCreateDTO{email='{$this->email}', nom='{$this->nom}', prenom='{$this->prenom}', date_naissance='{$this->date_naissance->toDateString()}', mot_de_passe='{$this->mot_de_passe}', id_sexe={$this->id_sexe}}";
    }
}
