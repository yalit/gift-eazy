<?php

namespace App\DataFixtures;

use App\Entity\Security\Factory\PasswordResetRequestTokenFactory;
use App\Entity\Security\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordResetTokenFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference("user_01", User::class);
        $notUsedResetToken = PasswordResetRequestTokenFactory::create($user->getEmail());
        $manager->persist($notUsedResetToken);

        $usedResetToken = PasswordResetRequestTokenFactory::create($user->getEmail());
        $usedResetToken->setUsed(true);
        $manager->persist($usedResetToken);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
