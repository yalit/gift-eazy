<?php

namespace App\Process\Event;

use App\Entity\Event\Factory\EventFactory;
use App\Mail\Event\EventCreationMail;
use App\Mail\HTMLEmailFactory;
use App\Repository\Event\EventRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

#[AsMessageHandler]
readonly class EventCreationProcess
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private HTMLEmailFactory $HTMLEmailFactory,
        private MailerInterface $mailer,
        private string $notificationEmailSender,
        private readonly UrlGeneratorInterface $router,
    ) {
    }

    public function __invoke(EventCreation $eventCreation): void
    {
        $event = EventFactory::createFromDTO($eventCreation);

        try {
            $this->eventRepository->save($event);
        } catch (\Throwable $e) {
            return;
        }

        $this->mailer->send($this->HTMLEmailFactory->generate(
            EventCreationMail::class,
            $this->notificationEmailSender,
            $event->getOrganizerEmail(),
            ['name' => $event->getName(), 'targetUrl' => $this->router->generate('event_update_anonymous', ['token' => $event->getToken()], UrlGeneratorInterface::ABSOLUTE_URL)]
        ));
    }
}
