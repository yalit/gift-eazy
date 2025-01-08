<?php

namespace App\Process\Security;

use App\Entity\Security\User;
use App\Process\SyncMessage;
use App\Validation\Security\ExistingUserEmail;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordResetRequest implements SyncMessage
{
    #[ExistingUserEmail]
    #[NotBlank]
    private string $email;

    public function __construct(
    )
    {}

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
