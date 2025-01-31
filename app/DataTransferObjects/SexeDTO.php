<?php

namespace App\DataTransferObjects;

class SexeDTO
{
    private int $id;
    private string $sexe;

    
    public function __construct(int $id, string $sexe)
    {
        $this->id = $id;
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

    public function getSexe(): string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): void
    {
        $this->sexe = $sexe;
    }

     
    public function __toString(): string
    {
        return "SexeDTO{id={$this->id}, sexe='{$this->sexe}'}";
    }
}
