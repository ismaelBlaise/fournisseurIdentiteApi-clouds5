<?php

namespace App\DataTransferObjects;

class ValidationPinDTO
{
    private string $email;
    private int $codepin;

    
    public function __construct(string $email, int $codepin)
    {
        $this->email = $email;
        $this->codepin = $codepin;
    }

     
    public function getEmail(): string
    {
        return $this->email;
    }

     
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

     
    public function getCodepin(): int
    {
        return $this->codepin;
    }

     
    public function setCodepin(int $codepin): void
    {
        $this->codepin = $codepin;
    }

     
    public function __toString(): string
    {
        return "ValidationPinDTO{email='{$this->email}', codepin={$this->codepin}}";
    }
}
