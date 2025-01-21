<?php

namespace App\Process\Event;

use App\Entity\Event\DTO\EventParticipantInputDTO;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
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

    /**
     * @var ArrayCollection<array-key, EventParticipantInputDTO>
     */
    #[Count(min: 2)]
    public Collection $participants;

    public function __construct()
    {
        $this->date = new DateTimeImmutable();
        $this->participants = new ArrayCollection();
    }

    public function addParticipant(EventParticipantInputDTO $participant): void
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }
    }

    public function removeParticipant(EventParticipantInputDTO $participant): void
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
        }
    }
}
