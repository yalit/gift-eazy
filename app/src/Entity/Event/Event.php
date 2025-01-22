<?php

namespace App\Entity\Event;

use App\Enum\EventStatus;
use App\Repository\Event\EventRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[UniqueEntity('token')]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[NotBlank]
    #[NotNull]
    private string $token;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[NotNull]
    #[NotBlank]
    private string $name;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[GreaterThanOrEqual('now')]
    private DateTimeImmutable $date;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[NotBlank]
    #[NotNull]
    #[Email]
    private string $organizerEmail;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $organizerName = null;

    #[ORM\Column(type: Types::STRING, length: 255, enumType: EventStatus::class)]
    private EventStatus $status = EventStatus::DRAFT;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $theme = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $maximumAmount = 0;

    /**
     * @var ArrayCollection<array-key,EventParticipant> $participants
     */
    #[ORM\OneToMany(targetEntity: EventParticipant::class, mappedBy: 'event', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $participants;

    public function __construct()
    {
        $this->date = new DateTimeImmutable();
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
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

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMaximumAmount(): int
    {
        return $this->maximumAmount;
    }

    public function setMaximumAmount(int $maximumAmount): static
    {
        $this->maximumAmount = $maximumAmount;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return ArrayCollection<array-key, EventParticipant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(EventParticipant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->setEvent($this);
        }

        return $this;
    }

    public function removeParticipant(EventParticipant $participant): static
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
        }

        return $this;
    }
}
