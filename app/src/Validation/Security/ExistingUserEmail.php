<?php

namespace App\Validation\Security;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ExistingUserEmail extends Constraint
{
    public string $message = 'validation.security.existing_user_email';
}
