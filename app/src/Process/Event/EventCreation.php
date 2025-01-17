<?php

namespace App\Process\Event;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class EventCreation
{
    #[NotNull]
    #[NotBlank]
    public ?string $name = null;

    #[GreaterThanOrEqual(new DateTimeImmutable())]
    public DateTimeImmutable $date;

    #[NotNull]
    #[NotBlank]
    #[Email]
    public ?string $organizerEmail = null;

    public ?string $organizerName = null;
    public ?string $theme = null;
    public ?string $description = null;

    #[GreaterThanOrEqual(0)]
    public int $maximumAmount = 0;

    public function __construct()
    {
        $this->date = new DateTimeImmutable();
    }
}
