<?php

namespace App\Entity;

use App\Enum\EventStatus;
use App\Repository\EventRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name = '';

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $date;

    #[ORM\Column(length: 255)]
    private string $organizerEmail = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $organizerName = null;

    #[ORM\Column(length: 255, enumType: EventStatus::class)]
    private EventStatus $status = EventStatus::DRAFT;

    public function __construct()
    {
        $this->date = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getOrganizerEmail(): string
    {
        return $this->organizerEmail;
    }

    public function setOrganizerEmail(string $organizerEmail): static
    {
        $this->organizerEmail = $organizerEmail;

        return $this;
    }

    public function getOrganizerName(): ?string
    {
        return $this->organizerName;
    }

    public function setOrganizerName(?string $organizerName): static
    {
        $this->organizerName = $organizerName;

        return $this;
    }

    public function getStatus(): EventStatus
    {
        return $this->status;
    }

    public function setStatus(EventStatus $status): static
    {
        $this->status = $status;

        return $this;
    }
}
