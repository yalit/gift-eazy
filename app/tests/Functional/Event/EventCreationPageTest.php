<?php

namespace App\Tests\Functional\Event;

use App\DataFixtures\UserFixtures;
use App\Tests\Functional\AbstractAppWebTestCase;

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
