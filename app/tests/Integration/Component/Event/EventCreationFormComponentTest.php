<?php

namespace App\Tests\Integration\Component\Event;

use App\Entity\Event\Event;
use App\Repository\Event\EventRepository;
use App\Twig\Components\EventForm;
use DateTimeImmutable;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;

class EventCreationFormComponentTest extends KernelTestCase
{
    use InteractsWithLiveComponents;

    private TranslatorInterface $translator;

    public function setUp(): void
    {
        $this->translator = self::getContainer()->get('translator');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->translator);
    }

    public function testRenderComponent(): void
    {
        $component = $this->createLiveComponent(EventForm::class);
        $renderedComponent = $component->render();

        self::assertStringContainsString($this->translator->trans('pages.event_creation.form.submit'), $renderedComponent);
    }

    public function testSuccessfulSubmitForm(): void
    {
        $component = $this->createLiveComponent(EventForm::class);

        $faker = Factory::create();
        $name = $faker->sentence(1);
        $organizerEmail = $faker->email();
        $organizerName = $faker->name();
        $date = DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+1 day', "+10 months"));
        $theme = $faker->word();
        $description = $faker->sentence(10);
        $maximumAmount = $faker->numberBetween(1, 100);

        $component->call('addCollectionItem', ['name' => 'participants']);
        $component->call('addCollectionItem', ['name' => 'participants']);
        $component->submitForm(['event_creation' => [
            'name' => $name,
            'organizerEmail' => $organizerEmail,
            'organizerName' => $organizerName,
            'date' => $date->format('Y-m-d'),
            'theme' => $theme,
            'description' => $description,
            'maximumAmount' => $maximumAmount,
            'participants' => [
                [
                    'name' => $faker->name(),
                    'email' => $faker->email()
                ],
                [
                    'email' => $faker->email(),
                    'name' => $faker->name()
                ],
            ]
        ]], 'save');

        $eventRepository = static::getContainer()->get(EventRepository::class);

        /** @var ?Event $event */
        $event = $eventRepository->findOneBy(['name' => $name]);
        self::assertNotNull($event);
        self::assertCount(3, $event->getParticipants());
    }
}
