<?php

namespace App\DataTransferObjects;

class ConnexionDTO
{
    private string $email;
    private string $motDePasse;

     
    public function __construct(string $email, string $motDePasse)
    {
        $this->email = $email;
        $this->motDePasse = $motDePasse;
    }

     
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): void
    {
        $this->motDePasse = $motDePasse;
    }

     
    public function __toString(): string
    {
        return "ConnexionDTO{email='{$this->email}', motDePasse='{$this->motDePasse}'}";
    }
}
