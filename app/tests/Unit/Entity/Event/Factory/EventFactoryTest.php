<?php

namespace App\Tests\Unit\Entity\Event\Factory;

use App\Entity\Event\Factory\EventFactory;
use App\Enum\EventStatus;
use App\Process\Event\EventCreation;
use DateTimeImmutable;
use Faker\Factory;
use PHPUnit\Framework\TestCase;

class EventFactoryTest extends TestCase
{
    public function testCreateFromDTO(): void
    {
        $faker = Factory::create();
        $eventCreationDTO = new EventCreation();
        $eventCreationDTO->name = $faker->sentence(1);
        $eventCreationDTO->organizerName = $faker->name;
        $eventCreationDTO->organizerEmail = $faker->email;
        $eventCreationDTO->description = $faker->text;
        $eventCreationDTO->date = DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months'));
        $eventCreationDTO->maximumAmount = 100;

        $event = EventFactory::createFromDTO($eventCreationDTO);

        self::assertGreaterThan(new DateTimeImmutable(), $event->getDate());
        /** @phpstan-ignore-next-line */
        self::assertNotNull($event->getToken());
        self::assertEquals(EventStatus::DRAFT, $event->getStatus());
    }
}
