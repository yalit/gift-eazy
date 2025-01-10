<?php

namespace App\Validation\Security;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * validates that the pair token and email is existing
 */
#[Attribute]
class ValidTokenAndEmailPairingForPasswordReset extends Constraint
{
    public string $message = 'validation.security.valid_token_and_email_pairing_for_password_reset';
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
