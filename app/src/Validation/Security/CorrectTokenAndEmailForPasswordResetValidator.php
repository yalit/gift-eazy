<?php

namespace App\Validation\Security;

use App\Process\Security\PasswordReset;
use App\Repository\Security\PasswordResetTokenRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class CorrectTokenAndEmailForPasswordResetValidator extends ConstraintValidator
{
    public function __construct(private readonly PasswordResetTokenRepository $resetRequestTokenRepository)
    {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CorrectTokenAndEmailForPasswordReset) {
            throw new UnexpectedTypeException($constraint, CorrectTokenAndEmailForPasswordReset::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof PasswordReset) {
            throw new UnexpectedValueException($value, PasswordReset::class);
        }

        $resetToken = $this->resetRequestTokenRepository->findTokenForEmail($value->getToken(), $value->getEmail());

        if (null === $resetToken || $resetToken->isUsed()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
