<?php

namespace App\Entity\Event\Factory;

use App\Entity\Event\Event;
use App\Enum\EventStatus;
use DateTimeImmutable;

final class EventFactory
{
    public static function create(
        string             $eventName,
        string             $organizerEmail,
        string             $organizerName = '',
        ?DateTimeImmutable $date = null,
        string             $description = '',
        string             $theme = '',
        EventStatus        $status = EventStatus::DRAFT,
        int                $maximumAmount = 0
    ): Event {
        $event = new Event();

        $event->setName($eventName);
        $event->setOrganizerEmail($organizerEmail);
        $event->setOrganizerName($organizerName);
        $event->setDate($date ?? new DateTimeImmutable());
        $event->setDescription($description);
        $event->setTheme($theme);
        $event->setStatus($status);
        $event->setMaximumAmount($maximumAmount);

        return $event;
    }
}
