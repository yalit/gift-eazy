<?php

namespace App\Process\Security;

use App\Process\SyncMessage;
use App\Validation\Security\CorrectTokenAndEmailForPasswordReset;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[CorrectTokenAndEmailForPasswordReset]
final class PasswordReset implements SyncMessage
{
    private string $token;
    private string $email;
    #[PasswordStrength]
    private string $plainPassword;

    public function __construct()
    {}

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }
}
