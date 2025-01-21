<?php

namespace App\Tests\Integration\Process\Entity;

use App\Entity\Event\DTO\EventParticipantInputDTO;
use App\Process\Event\EventCreation;
use App\Repository\Event\EventRepository;
use DateTimeImmutable;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

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
            $faker->text(),
            DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
            $faker->numberBetween(10, 250),
            [
                ['name' => $faker->name(), 'email' => $faker->email()],
                ['name' => $faker->name(), 'email' => $faker->email()],
            ]
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

        self::assertCount(3, $createdEvent->getParticipants());
        foreach ($createdEvent->getParticipants() as $participant) {
            if (!($participant->getName() === $eventCreation->organizerName && $participant->getEmail() === $eventCreation->organizerEmail)) {
                self::assertCount(1, $eventCreation->participants->filter(function (EventParticipantInputDTO $eventParticipant) use ($participant) {
                    return $participant->getEmail() === $eventParticipant->email && $participant->getName() === $eventParticipant->name;
                }));
            } else {
                self::assertEquals($eventCreation->organizerName, $participant->getName());
                self::assertEquals($eventCreation->organizerEmail, $participant->getEmail());
            }
            self::assertNotNull($participant->getToken());
        }
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
                $faker->text(),
                DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
                $faker->numberBetween(10, 250),
                [
                    ['name' => $faker->name(), 'email' => $faker->email()],
                    ['name' => $faker->name(), 'email' => $faker->email()],
                ]
            )
        ];
        yield "Blank Name" => [
            $this->getEventCreation(
                "",
                $faker->email(),
                $faker->name(),
                $faker->word(),
                $faker->text(),
                DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
                $faker->numberBetween(10, 250),
                [
                    ['name' => $faker->name(), 'email' => $faker->email()],
                    ['name' => $faker->name(), 'email' => $faker->email()],
                ]
            )
        ];
        yield "No Organizer email" => [
            $this->getEventCreation(
                $faker->sentence(1),
                null,
                $faker->name(),
                $faker->word(),
                $faker->text(),
                DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
                $faker->numberBetween(10, 250),
                [
                ['name' => $faker->name(), 'email' => $faker->email()],
                    ['name' => $faker->name(), 'email' => $faker->email()],
                ]
            )
        ];
        yield "Blank Organizer email" => [
            $this->getEventCreation(
                $faker->sentence(1),
                "",
                $faker->name(),
                $faker->word(),
                $faker->text(),
                DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
                $faker->numberBetween(10, 250),
                [
                    ['name' => $faker->name(), 'email' => $faker->email()],
                    ['name' => $faker->name(), 'email' => $faker->email()],
                ]
            )
        ];
        yield "Organizer incorrect email" => [
            $this->getEventCreation(
                $faker->sentence(1),
                $faker->name(),
                $faker->name(),
                $faker->word(),
                $faker->text(),
                DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
                $faker->numberBetween(10, 250),
                [
                    ['name' => $faker->name(), 'email' => $faker->email()],
                    ['name' => $faker->name(), 'email' => $faker->email()],
                ]
            )
        ];
        yield "Date in the past" => [
            $this->getEventCreation(
                $faker->sentence(1),
                $faker->email(),
                $faker->name(),
                $faker->word(),
                $faker->text(),
                DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 month', '-1 day')),
                $faker->numberBetween(10, 250),
                [
                    ['name' => $faker->name(), 'email' => $faker->email()],
                    ['name' => $faker->name(), 'email' => $faker->email()],
                ]
            )
        ];
        yield "Negative maximum amount" => [
            $this->getEventCreation(
                $faker->sentence(1),
                $faker->email(),
                $faker->name(),
                $faker->word(),
                $faker->text(),
                DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
                $faker->numberBetween(-110, -1),
                [
                    ['name' => $faker->name(), 'email' => $faker->email()],
                    ['name' => $faker->name(), 'email' => $faker->email()],
                ]
            )
        ];
        yield "Insufficient number of participants" => [
            $this->getEventCreation(
                $faker->sentence(1),
                $faker->email(),
                $faker->name(),
                $faker->word(),
                $faker->text(),
                DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months')),
                $faker->numberBetween(10, 100),
                [
                    ['name' => $faker->name(), 'email' => $faker->email()],
                ]
            )
        ];
    }

    /**
     * @param array<int, array{name: string, email: string}> $participants
     */
    public function getEventCreation(
        ?string $name = null,
        ?string $organizerEmail = null,
        ?string $organizerName = null,
        ?string $theme = null,
        ?string $description = null,
        DateTimeImmutable $date = new DateTimeImmutable(),
        int $maximumAmount = 0,
        array $participants = [],
    ): EventCreation {
        $eventCreation = new EventCreation();

        $eventCreation->name = $name;
        $eventCreation->organizerEmail = $organizerEmail;
        $eventCreation->organizerName = $organizerName;
        $eventCreation->theme = $theme;
        $eventCreation->description = $description;
        $eventCreation->date = $date;
        $eventCreation->maximumAmount = $maximumAmount;
        foreach ($participants as $participant) {
            $eventParticipantDTO = new EventParticipantInputDTO();
            $eventParticipantDTO->name = $participant['name'];
            $eventParticipantDTO->email = $participant['email'];
            $eventCreation->addParticipant($eventParticipantDTO);
        }

        return $eventCreation;
    }
}
