<?php

namespace App\Tests\Unit\Validation\Security;

use App\Entity\Security\Factory\PasswordResetRequestTokenFactory;
use App\Entity\Security\PasswordResetToken;
use App\Entity\Security\User;
use App\Process\Security\PasswordReset;
use App\Repository\Security\PasswordResetTokenRepository;
use App\Repository\Security\UserRepository;
use App\Validation\Security\ValidTokenAndEmailPairingForPasswordReset;
use App\Validation\Security\ValidTokenAndEmailPairingForPasswordResetValidator;
use App\Validation\Security\ExistingUserEmail;
use App\Validation\Security\ExistingUserEmailValidator;
use PHPUnit\Framework\MockObject\MockClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ExistingUserEmailValidatorTest extends ConstraintValidatorTestCase
{
    private MockClass|UserRepository $userRepository;

    public function testNoValidationErrorForConstraintValidator(): void
    {
        $email = "existing_user@email.com";
        $this->userRepository
            ->expects($this->once())
            ->method("findUserByMail")
            ->with($email)
            ->willReturn((new User())->setEmail($email));

        $this->validator->validate($email, new ExistingUserEmail());
        $this->assertNoViolation();
    }

    public function testValidationErrorForConstraintValidator(): void
    {
        $email = 'non_existing_user@email.com';
        $this->userRepository
            ->expects($this->once())
            ->method("findUserByMail")
            ->with($email)
            ->willReturn(null);

        $this->validator->validate($email, new ExistingUserEmail());
        $this->buildViolation((new ExistingUserEmail())->message)
            ->assertRaised();
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        return new ExistingUserEmailValidator($this->userRepository);
    }
}
