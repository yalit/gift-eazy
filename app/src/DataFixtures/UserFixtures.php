<?php

namespace App\DataFixtures;

use App\Entity\Security\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_PASSWORD = "Password123";
    public const FIRST_USER_EMAIL = "user01@email.com";

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail(self::FIRST_USER_EMAIL);
        $user->setName("user01");
        $user->setPassword($this->passwordHasher->hashPassword($user, self::USER_PASSWORD));

        $manager->persist($user);

        $this->setReference('user_01', $user);
        $manager->flush();
    }
}
