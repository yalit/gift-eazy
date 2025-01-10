<?php

namespace App\Validation\Security;

use App\Process\Security\PasswordReset;
use App\Repository\Security\PasswordResetTokenRepository;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ValidPasswordResetTokenValidator extends ConstraintValidator
{
    public function __construct(private readonly PasswordResetTokenRepository $resetRequestTokenRepository)
    {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidPasswordResetToken) {
            throw new UnexpectedTypeException($constraint, ValidPasswordResetToken::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            return;
        }

        $resetToken = $this->resetRequestTokenRepository->findOneBy(['token' => $value]);

        if (
            null === $resetToken
            || $resetToken->isUsed()
            || $resetToken->getExpirationDate() < new DateTimeImmutable()
        ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
