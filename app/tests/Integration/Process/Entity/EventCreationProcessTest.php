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
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class EventCreationProcessTest extends KernelTestCase
{
    private EventRepository $eventRepository;
    private ValidatorInterface $validator;
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->eventRepository = self::getContainer()->get(EventRepository::class);
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
        $this->translator = self::getContainer()->get(TranslatorInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->eventRepository);
        unset($this->validator);
        unset($this->translator);
    }

    public function testCorrectEventCreation(): void
    {
        $faker = Factory::create();
        $eventCreation = new EventCreation();
        $eventCreation->name = $faker->sentence(1);
        $eventCreation->organizerName = $faker->name;
        $eventCreation->organizerEmail = $faker->email;
        $eventCreation->description = $faker->text;
        $eventCreation->date = DateTimeImmutable::createFromMutable($faker->dateTimeInInterval('now', '+10 months'));
        $eventCreation->maximumAmount = 100;

        $process = new EventCreationProcess($this->eventRepository);
        $process($eventCreation);

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

    public function testIncorrectEventCreation(): void
    {
        //TODO :: create the incorrect test
    }
}
