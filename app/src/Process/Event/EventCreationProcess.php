<?php

namespace App\Process\Event;

use App\Entity\Event\Factory\EventFactory;
use App\Repository\Event\EventRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class EventCreationProcess
{
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {
    }

    public function __invoke(EventCreation $eventCreation): void
    {
        $event = EventFactory::createFromDTO($eventCreation);

        $this->eventRepository->save($event);
    }
}
