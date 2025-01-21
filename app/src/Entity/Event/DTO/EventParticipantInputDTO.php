<?php

namespace App\Entity\Event\DTO;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class EventParticipantInputDTO
{
    #[NotBlank]
    #[NotNull]
    public ?string $name = null;

    #[NotBlank]
    #[NotNull]
    #[Email]
    public ?string $email = null;
}
