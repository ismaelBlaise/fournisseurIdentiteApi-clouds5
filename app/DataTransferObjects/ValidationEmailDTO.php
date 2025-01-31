<?php

namespace App\DataTransferObjects;

class ValidationEmailDTO
{
    private string $token;

    
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    
    public function getToken(): string
    {
        return $this->token;
    }

     
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

     
    public function __toString(): string
    {
        return "ValidationEmailDTO{token='{$this->token}'}";
    }
}
