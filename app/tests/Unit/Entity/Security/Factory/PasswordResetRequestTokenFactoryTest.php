<?php

namespace App\Tests\Unit\Entity\Security\Factory;

use App\Entity\Security\Factory\PasswordResetRequestTokenFactory;
use App\Entity\Security\PasswordResetToken;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PasswordResetRequestTokenFactoryTest extends TestCase
{
    public function testSuccessfulCreatePasswordResetRequestToken(): void
    {
        $factory = new PasswordResetRequestTokenFactory();
        $email = "test@email.com";

        $token = $factory->create($email);
        self::assertEquals($email, $token->getEmail());
        self::assertNotEquals("", $token->getToken());
        self::assertGreaterThan(new DateTimeImmutable(), $token->getExpirationDate());

        self::assertLessThan((new DateTimeImmutable())->add(new DateInterval(sprintf("PT%dH", PasswordResetToken::EXPIRATION_HOURS))), $token->getExpirationDate());
        self::assertLessThanOrEqual(new DateTimeImmutable(), $token->getCreatedAt());
    }
}
