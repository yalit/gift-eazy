<?php

namespace App\Entity\Security\Factory;

use App\Entity\Security\PasswordResetToken;
use DateInterval;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class PasswordResetRequestTokenFactory
{
    public static function create(string $email): PasswordResetToken
    {
        $token = new PasswordResetToken();
        $token->setEmail($email);
        $token->setToken(Uuid::v4()->toString());
        $token->setExpirationDate((new DateTimeImmutable())->add(new DateInterval(sprintf("PT%dH", PasswordResetToken::EXPIRATION_HOURS))));

        return $token;
    }
}
