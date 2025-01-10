<?php

namespace App\Tests\Unit\Validation\Security;

use App\Entity\Security\Factory\PasswordResetRequestTokenFactory;
use App\Entity\Security\PasswordResetToken;
use App\Process\Security\PasswordReset;
use App\Repository\Security\PasswordResetTokenRepository;
use App\Validation\Security\ValidPasswordResetToken;
use App\Validation\Security\ValidTokenAndEmailPairingForPasswordReset;
use App\Validation\Security\ValidTokenAndEmailPairingForPasswordResetValidator;
use App\Validation\Security\ValidPasswordResetTokenValidator;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockClass;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @template-extends ConstraintValidatorTestCase<ValidPasswordResetTokenValidator>
 */
class ValidPasswordResetTokenValidatorTest extends ConstraintValidatorTestCase
{
    private MockObject&PasswordResetTokenRepository $passwordResetTokenRepository;

    public function testNoValidationErrorForConstraintValidator(): void
    {
        $resetToken = $this->getResetToken();
        $this->passwordResetTokenRepository
            ->expects($this->once())
            ->method("findOneBy")
            ->with(['token' => $resetToken->getToken()])
            ->willReturn($resetToken);

        $this->validator->validate($resetToken->getToken(), new ValidPasswordResetToken());
        $this->assertNoViolation();
    }

    public function testUnknownTokenLeadsToValidationError(): void
    {
        $unknownToken = "unknown_token";
        $this->passwordResetTokenRepository
            ->expects($this->once())
            ->method("findOneBy")
            ->with(['token' => $unknownToken])
            ->willReturn(null);

        $this->validator->validate($unknownToken, new ValidPasswordResetToken());
        $this->buildViolation((new ValidPasswordResetToken())->message)
            ->assertRaised();
    }

    public function testUsedTokenLeadsToValidationError(): void
    {
        $resetToken = $this->getResetToken();
        $resetToken->setUsed(true);
        $this->passwordResetTokenRepository
            ->expects($this->once())
            ->method("findOneBy")
            ->with(['token' => $resetToken->getToken()])
            ->willReturn($resetToken);

        $this->validator->validate($resetToken->getToken(), new ValidPasswordResetToken());
        $this->buildViolation((new ValidPasswordResetToken())->message)
            ->assertRaised();
    }

    public function testExpiredTokenLeadsToValidationError(): void
    {
        $resetToken = $this->getResetToken();
        $resetToken->setExpirationDate((new DateTimeImmutable())->sub(new DateInterval("PT1M")));
        $this->passwordResetTokenRepository
            ->expects($this->once())
            ->method("findOneBy")
            ->with(['token' => $resetToken->getToken()])
            ->willReturn($resetToken);

        $this->validator->validate($resetToken->getToken(), new ValidPasswordResetToken());
        $this->buildViolation((new ValidPasswordResetToken())->message)
            ->assertRaised();
    }
    protected function createValidator(): ConstraintValidatorInterface
    {
        $this->passwordResetTokenRepository = $this->createMock(PasswordResetTokenRepository::class);
        return new ValidPasswordResetTokenValidator($this->passwordResetTokenRepository);
    }

    private function getResetToken(): PasswordResetToken
    {
        return PasswordResetRequestTokenFactory::create("test@email.com");
    }
}
