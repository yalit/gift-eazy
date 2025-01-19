<?php

namespace App\Entity\Event;

use App\Repository\EventParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: EventParticipantRepository::class)]
class EventParticipant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    #[NotBlank]
    #[NotNull]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    #[NotBlank]
    #[NotNull]
    #[Email]
    private string $email;

    #[ORM\Column(type: Types::STRING ,length: 255, nullable: false)]
    #[NotBlank]
    #[NotNull]
    private string $token;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'participants')]
    private Event $event;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): static
    {
        $this->event = $event;

        return $this;
    }
}
