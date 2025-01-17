<?php

namespace App\DataFixtures;

use App\Entity\Event\Factory\EventFactory;
use App\Enum\EventStatus;
use App\Process\Event\EventCreation;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
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
            $DTO = $this->getCreationDTO(
                $faker->sentence(2),
                UserFixtures::FIRST_USER_EMAIL,
                organizerName: $faker->name(),
                date: DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+2 months')),
                description: $faker->text(),
                theme: $faker->sentence(1),
                maximumAmount: $faker->numberBetween(10, 250),
            );
            $event = EventFactory::createFromDTO($DTO);
            $event->setStatus($status);
            $manager->persist($event);
        }

        // for non-registered user
        foreach (EventStatus::cases() as $status) {
            $DTO = $this->getCreationDTO(
                $faker->sentence(2),
                self::NON_REGISTERED_USER_EMAIL,
                organizerName: $faker->name(),
                date: DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+2 months')),
                description: $faker->text(),
                theme: $faker->sentence(1),
                maximumAmount: $faker->numberBetween(10, 250),
            );
            $event = EventFactory::createFromDTO($DTO);
            $event->setStatus($status);
            $manager->persist($event);
        }
        $manager->flush();
    }

    private function getCreationDTO(
        string $eventName,
        string $organizerEmail,
        string $organizerName = '',
        ?DateTimeImmutable $date = null,
        string $description = '',
        string $theme = '',
        int $maximumAmount = 0
    ): EventCreation {
        $DTO = new EventCreation();

        $DTO->name = $eventName;
        $DTO->organizerEmail = $organizerEmail;
        $DTO->organizerName = $organizerName;
        $DTO->date = $date;
        $DTO->description = $description;
        $DTO->theme = $theme;
        $DTO->maximumAmount = $maximumAmount;

        return $DTO;
    }
}
