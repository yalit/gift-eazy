<?php

namespace App\Tests\Functional\Event;

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

    // TODO :: test incorrect access to the organizer event creation page
    // TODO :: test correct access after login to the organizer event creation page
}
