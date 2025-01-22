<?php

namespace App\Entity\Event\Factory;

use App\Entity\Event\Event;
use App\Process\Event\EventCreation;
use Symfony\Component\Uid\Uuid;

final class EventFactory
{
    public static function createFromDTO(EventCreation $DTO): Event
    {
        $event = new Event();
        $event->setName($DTO->name);
        $event->setOrganizerEmail($DTO->organizerEmail);
        $event->setOrganizerName($DTO->organizerName);
        $event->setDate($DTO->date);
        $event->setDescription($DTO->description);
        $event->setTheme($DTO->theme);
        $event->setMaximumAmount($DTO->maximumAmount);
        $event->setToken(Uuid::v4()->toString());

        // add the organizer as a participant as well
        $event->addParticipant(EventParticipantFactory::create($DTO->organizerName, $DTO->organizerEmail));

        foreach ($DTO->participants as $participant) {
            $event->addParticipant(EventParticipantFactory::create($participant->name, $participant->email));
        }

        return $event;
    }
}
