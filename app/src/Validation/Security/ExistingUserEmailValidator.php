<?php

namespace App\Validation\Security;

use App\Process\Security\PasswordReset;
use App\Repository\Security\PasswordResetTokenRepository;
use App\Repository\Security\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ExistingUserEmailValidator extends ConstraintValidator
{
    public function __construct(private readonly UserRepository $userRepository)
    {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingUserEmail) {
            throw new UnexpectedTypeException($constraint, ExistingUserEmail::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, "string");
        }

        $user = $this->userRepository->findUserByMail($value);

        if (null === $user) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
