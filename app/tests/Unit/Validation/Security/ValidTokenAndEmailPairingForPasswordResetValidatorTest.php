<?php

namespace App\Tests\Unit\Validation\Security;

use App\Entity\Security\Factory\PasswordResetRequestTokenFactory;
use App\Entity\Security\PasswordResetToken;
use App\Process\Security\PasswordReset;
use App\Repository\Security\PasswordResetTokenRepository;
use App\Validation\Security\ValidTokenAndEmailPairingForPasswordReset;
use App\Validation\Security\ValidTokenAndEmailPairingForPasswordResetValidator;
use PHPUnit\Framework\MockObject\MockClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ValidTokenAndEmailPairingForPasswordResetValidatorTest extends ConstraintValidatorTestCase
{
    private MockClass|PasswordResetTokenRepository $passwordResetTokenRepository;

    public function testNoValidationErrorForConstraintValidator(): void
    {
        $resetToken = $this->getResetToken();
        $this->passwordResetTokenRepository
            ->expects($this->once())
            ->method("findTokenForEmailAndToken")
            ->with($resetToken->getToken(), $resetToken->getEmail())
            ->willReturn($resetToken);

        $passwordReset = new PasswordReset();
        $passwordReset->setToken($resetToken->getToken());
        $passwordReset->setEmail($resetToken->getEmail());

        $this->validator->validate($passwordReset, new ValidTokenAndEmailPairingForPasswordReset());
        $this->assertNoViolation();
    }

    public function testValidationErrorForConstraintValidatorWithNotFoundToken(): void
    {
        $resetToken = $this->getResetToken();
        $this->passwordResetTokenRepository
            ->expects($this->once())
            ->method("findTokenForEmailAndToken")
            ->with($resetToken->getToken(), $resetToken->getEmail())
            ->willReturn(null);

        $passwordReset = new PasswordReset();
        $passwordReset->setToken($resetToken->getToken());
        $passwordReset->setEmail($resetToken->getEmail());

        $this->validator->validate($passwordReset, new ValidTokenAndEmailPairingForPasswordReset());
        $this->buildViolation((new ValidTokenAndEmailPairingForPasswordReset())->message)
            ->assertRaised();
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        $this->passwordResetTokenRepository = $this->createMock(PasswordResetTokenRepository::class);
        return new ValidTokenAndEmailPairingForPasswordResetValidator($this->passwordResetTokenRepository);
    }

    private function getResetToken(): PasswordResetToken
    {
        return PasswordResetRequestTokenFactory::create("test@email.com");
    }
}
