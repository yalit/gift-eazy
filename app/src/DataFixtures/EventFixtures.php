<?php

namespace App\DataFixtures;

use App\Entity\Event\Event;
use App\Entity\Event\Factory\EventFactory;
use App\Enum\EventStatus;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;

class EventFixtures extends Fixture
{
    public const NON_REGISTERED_USER_EMAIL = "non_registered_user@email.com";

    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        // for registered user
        foreach (EventStatus::cases() as $status) {
            $manager->persist(
                EventFactory::create(
                    $faker->sentence(2),
                    UserFixtures::FIRST_USER_EMAIL,
                    organizerName: $faker->name(),
                    date: DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+2 months')),
                    description: $faker->text(),
                    theme: $faker->sentence(1),
                    status: $status,
                    maximumAmount: $faker->numberBetween(10, 250),
                )
            );
        }

        // for non-registered user
        foreach (EventStatus::cases() as $status) {
            $manager->persist(
                EventFactory::create(
                    $faker->sentence(2),
                    self::NON_REGISTERED_USER_EMAIL,
                    organizerName: $faker->name(),
                    date: DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+2 months')),
                    description: $faker->text(),
                    theme: $faker->sentence(1),
                    status: $status,
                    maximumAmount: $faker->numberBetween(10, 250),
                )
            );
        }
        $manager->flush();
    }
}
