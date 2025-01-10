<?php

namespace App\Validation\Security;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * validates that the Password Reset Token as string is:
 * - an existing token
 * - a not used token
 * - a non-expired token
 */
#[Attribute]
class ValidPasswordResetToken extends Constraint
{
    public string $message = 'validation.security.valid_password_reset_token_for_password_reset';
}
