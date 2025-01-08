<?php

namespace App\Validation\Security;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class CorrectTokenAndEmailForPasswordReset extends Constraint
{
    public string $message = 'validation.security.correct_token_and_email_for_password_reset';
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
