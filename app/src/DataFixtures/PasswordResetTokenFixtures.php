<?php

namespace App\DataFixtures;

use App\Entity\Security\Factory\PasswordResetRequestTokenFactory;
use App\Entity\Security\User;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordResetTokenFixtures extends Fixture implements DependentFixtureInterface
{
    public const USED_TOKEN_EMAIL = "used_token@email.com";
    public const EXPIRED_TOKEN_EMAIL = "expired_token@email.com";
    public function load(ObjectManager $manager): void
    {
        // Used Reset Token
        $usedResetToken = PasswordResetRequestTokenFactory::create(self::USED_TOKEN_EMAIL);
        $usedResetToken->setUsed(true);
        $manager->persist($usedResetToken);

        // Expired reset Token
        $expiredResetToken = PasswordResetRequestTokenFactory::create(self::EXPIRED_TOKEN_EMAIL);
        $expiredResetToken->setExpirationDate((new DateTimeImmutable())->sub(new DateInterval("PT1M")));
        $manager->persist($expiredResetToken);

        // Not used reset Token
        $notUsedResetToken = PasswordResetRequestTokenFactory::create(UserFixtures::FIRST_USER_EMAIL);
        $manager->persist($notUsedResetToken);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
