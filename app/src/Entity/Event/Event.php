<?php

namespace App\Entity\Event;

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
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name = '';

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $date;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $organizerEmail = '';

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $organizerName = null;

    #[ORM\Column(type: Types::STRING, length: 255, enumType: EventStatus::class)]
    private EventStatus $status = EventStatus::DRAFT;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $theme = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $description = '';

    #[ORM\Column(type: Types::INTEGER)]
    private int $maximumAmount = 0;

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

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getMaximumAmount(): int
    {
        return $this->maximumAmount;
    }

    public function setMaximumAmount(int $maximumAmount): void
    {
        $this->maximumAmount = $maximumAmount;
    }
}
