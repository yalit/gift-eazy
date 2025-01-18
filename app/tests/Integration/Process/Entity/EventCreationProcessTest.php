<?php

namespace App\Tests\Integration\Process\Entity;

use App\Entity\Event\Factory\EventFactory;
use App\Mail\HTMLEmailFactory;
use App\Process\Event\EventCreation;
use App\Process\Event\EventCreationProcess;
use App\Process\Security\PasswordReset;
use App\Process\Security\PasswordResetProcess;
use App\Repository\EventRepository;
use App\Repository\Security\PasswordResetTokenRepository;
use App\Repository\Security\UserRepository;
use DateTimeImmutable;
use Exception;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class EventCreationProcessTest extends KernelTestCase
{
    private EventRepository $eventRepository;
    private MessageBusInterface $messageBus;

    protected function setUp(): void
    {
        $this->eventRepository = self::getContainer()->get(EventRepository::class);
        $this->messageBus = self::getContainer()->get(MessageBusInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->eventRepository);
        unset($this->messageBus);
    }

    public function testCorrectEventCreation(): void
    {
        $faker = Factory::create();
        $eventCreation = $this->getEventCreation(
            $faker->sentence(1),
            $faker->email(),
            $faker->name(),
            $faker->word(),
            $faker->text,
            DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
            $faker->numberBetween(10, 250)
        );

        $this->messageBus->dispatch($eventCreation);

        $createdEvent = $this->eventRepository->findOneBy(['organizerEmail' => $eventCreation->organizerEmail, 'name' => $eventCreation->name]);
        self::assertNotNull($createdEvent);

        self::assertNotNull($createdEvent->getId());
        self::assertNotNull($createdEvent->getToken());
        self::assertEquals($eventCreation->organizerEmail, $createdEvent->getOrganizerEmail());
        self::assertEquals($eventCreation->organizerName, $createdEvent->getOrganizerName());
        self::assertEquals($eventCreation->theme, $createdEvent->getTheme());
        self::assertEquals($eventCreation->description, $createdEvent->getDescription());
        self::assertEquals($eventCreation->date, $createdEvent->getDate());
        self::assertEquals($eventCreation->maximumAmount, $createdEvent->getMaximumAmount());
    }

    /**
     * @dataProvider getIncorrectDTO
     */
    public function testInvalidEventCreation(EventCreation $eventCreation): void
    {
        $this->expectException(ValidationFailedException::class);
        $this->messageBus->dispatch($eventCreation);
    }

    /**
     * @return iterable<string, array<int, EventCreation>>
     */
    public function getIncorrectDTO(): iterable
    {
        $faker = Factory::create();
        yield "No Name" => [
            $this->getEventCreation(
                null,
                $faker->email(),
                $faker->name(),
                $faker->word(),
                $faker->text,
                DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
                $faker->numberBetween(10, 250)
            )
        ];
        yield "Blank Name" => [
            $this->getEventCreation(
                "",
                $faker->email(),
                $faker->name(),
                $faker->word(),
                $faker->text,
                DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
                $faker->numberBetween(10, 250)
            )
        ];
        yield "No Organizer email" => [
            $this->getEventCreation(
                $faker->sentence(1),
                null,
                $faker->name(),
                $faker->word(),
                $faker->text,
                DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
                $faker->numberBetween(10, 250)
            )
        ];
        yield "Blank Organizer email" => [
            $this->getEventCreation(
                $faker->sentence(1),
                "",
                $faker->name(),
                $faker->word(),
                $faker->text,
                DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
                $faker->numberBetween(10, 250)
            )
        ];
        yield "Organizer incorrect email" => [
            $this->getEventCreation(
                $faker->sentence(1),
                $faker->name(),
                $faker->name(),
                $faker->word(),
                $faker->text,
                DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
                $faker->numberBetween(10, 250)
            )
        ];
        yield "Date in the past" => [
            $this->getEventCreation(
                $faker->sentence(1),
                $faker->email(),
                $faker->name(),
                $faker->word(),
                $faker->text,
                DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', '-1 day')),
                $faker->numberBetween(10, 250)
            )
        ];
        yield "Negative maximum amount" => [
            $this->getEventCreation(
                $faker->sentence(1),
                $faker->email(),
                $faker->name(),
                $faker->word(),
                $faker->text,
                DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
                $faker->numberBetween(-110, -1)
            )
        ];
    }

    public function getEventCreation(
        ?string $name = null,
        ?string $organizerEmail = null,
        ?string $organizerName = null,
        ?string $theme = null,
        ?string $description = null,
        DateTimeImmutable $date = new DateTimeImmutable(),
        int $maximumAmount = 0,
    ): EventCreation {
        $eventCreation = new EventCreation();

        $eventCreation->name = $name;
        $eventCreation->organizerEmail = $organizerEmail;
        $eventCreation->organizerName = $organizerName;
        $eventCreation->theme = $theme;
        $eventCreation->description = $description;
        $eventCreation->date = $date;
        $eventCreation->maximumAmount = $maximumAmount;

        return $eventCreation;
    }
}
