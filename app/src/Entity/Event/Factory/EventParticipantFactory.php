<?php

namespace App\Entity\Event\Factory;

use App\Entity\Event\EventParticipant;
use Symfony\Component\Uid\Uuid;

final class EventParticipantFactory
{
    public static function create(string $name, string $email): EventParticipant
    {
        $participant = new EventParticipant();

        $participant->setName($name);
        $participant->setEmail($email);
        $participant->setToken(Uuid::v4()->toString());

        return $participant;
    }
}
