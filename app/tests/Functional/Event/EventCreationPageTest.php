<?php

namespace App\Tests\Functional\Event;

use App\DataFixtures\UserFixtures;
use App\Entity\Event\Event;
use App\Repository\EventRepository;
use App\Tests\Functional\AbstractAppWebTestCase;
use DateTimeImmutable;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use function PHPUnit\Framework\assertEquals;

final class EventCreationPageTest extends AbstractAppWebTestCase
{
    public function testPageSuccessful(): void
    {
        $this->client->request('GET', '/event/creation');
        self::assertResponseIsSuccessful();
    }

    public function testAnonymousUserUnsuccessfulAccessToOrganizerEventCreationPage(): void
    {
        $this->client->request('GET', '/organizer/event/creation');
        self::assertResponseRedirects('/login');
    }

    public function testOrganizerUserSuccessfulAccessToOrganizerEventCreationPage(): void
    {
        $this->loginUser(UserFixtures::FIRST_USER_EMAIL);
        $this->client->request('GET', '/organizer/event/creation');
        self::assertResponseIsSuccessful();
    }
}
