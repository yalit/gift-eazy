<?php

namespace App\Tests\Unit\Validation\Security;

use App\Entity\Security\Factory\PasswordResetRequestTokenFactory;
use App\Entity\Security\PasswordResetToken;
use App\Process\Security\PasswordReset;
use App\Repository\Security\PasswordResetTokenRepository;
use App\Validation\Security\CorrectTokenAndEmailForPasswordReset;
use App\Validation\Security\CorrectTokenAndEmailForPasswordResetValidator;
use PHPUnit\Framework\MockObject\MockClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CorrectTokenAndEmailForPasswordResetValidatorTest extends ConstraintValidatorTestCase
{
    private MockClass|PasswordResetTokenRepository $passwordResetTokenRepository;

    public function testNoValidationErrorForConstraintValidator(): void
    {
        $resetToken = $this->getResetToken();
        $this->passwordResetTokenRepository
            ->expects($this->once())
            ->method("findTokenForEmail")
            ->with($resetToken->getToken(), $resetToken->getEmail())
            ->willReturn($resetToken);

        $passwordReset = new PasswordReset();
        $passwordReset->setToken($resetToken->getToken());
        $passwordReset->setEmail($resetToken->getEmail());

        $this->validator->validate($passwordReset, new CorrectTokenAndEmailForPasswordReset());
        $this->assertNoViolation();
    }

    public function testValidationErrorForConstraintValidatorWithNotFoundToken(): void
    {
        $resetToken = $this->getResetToken();
        $this->passwordResetTokenRepository
            ->expects($this->once())
            ->method("findTokenForEmail")
            ->with($resetToken->getToken(), $resetToken->getEmail())
            ->willReturn(null);

        $passwordReset = new PasswordReset();
        $passwordReset->setToken($resetToken->getToken());
        $passwordReset->setEmail($resetToken->getEmail());

        $this->validator->validate($passwordReset, new CorrectTokenAndEmailForPasswordReset());
        $this->buildViolation((new CorrectTokenAndEmailForPasswordReset())->message)
            ->assertRaised();
    }

    public function testValidationErrorForConstraintValidatorWithUsedToken(): void
    {
        $resetToken = $this->getResetToken();
        $this->passwordResetTokenRepository
            ->expects($this->once())
            ->method("findTokenForEmail")
            ->with($resetToken->getToken(), $resetToken->getEmail())
            ->willReturn((new PasswordResetToken())->setUsed(true));

        $passwordReset = new PasswordReset();
        $passwordReset->setToken($resetToken->getToken());
        $passwordReset->setEmail($resetToken->getEmail());

        $this->validator->validate($passwordReset, new CorrectTokenAndEmailForPasswordReset());
        $this->buildViolation((new CorrectTokenAndEmailForPasswordReset())->message)
            ->assertRaised();
    }
    protected function createValidator(): ConstraintValidatorInterface
    {
        $this->passwordResetTokenRepository = $this->createMock(PasswordResetTokenRepository::class);
        return new CorrectTokenAndEmailForPasswordResetValidator($this->passwordResetTokenRepository);
    }

    private function getResetToken(): PasswordResetToken
    {
        return PasswordResetRequestTokenFactory::create("test@email.com");
    }
}
