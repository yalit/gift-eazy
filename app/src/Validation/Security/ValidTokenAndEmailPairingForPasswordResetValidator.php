<?php

namespace App\Validation\Security;

use App\Process\Security\PasswordReset;
use App\Repository\Security\PasswordResetTokenRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ValidTokenAndEmailPairingForPasswordResetValidator extends ConstraintValidator
{
    public function __construct(private readonly PasswordResetTokenRepository $resetRequestTokenRepository)
    {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidTokenAndEmailPairingForPasswordReset) {
            throw new UnexpectedTypeException($constraint, ValidTokenAndEmailPairingForPasswordReset::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof PasswordReset) {
            throw new UnexpectedValueException($value, PasswordReset::class);
        }

        $resetToken = $this->resetRequestTokenRepository->findTokenForEmailAndToken($value->getToken(), $value->getEmail());

        if (null === $resetToken) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
