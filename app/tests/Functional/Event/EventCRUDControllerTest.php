<?php

namespace App\Tests\Functional\Event;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class EventCRUDControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/event/creation');

        self::assertResponseIsSuccessful();
    }
}
