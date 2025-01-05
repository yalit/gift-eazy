<?php

namespace App\Twig\Components;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Hero extends AbstractController
{
    private const VERB_AM = "hero.verb.am";
    private const VERB_WANT_TO_BE = "hero.verb.want_to_be";

    private const SUBJECT_SANTA_ORGANIZER = "hero.subject.santa";
    private const SUBJECT_GIFTEE = "hero.subject.giftee";

    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $verb = self::VERB_WANT_TO_BE;

    #[LiveProp(writable: true)]
    public string $subject = self::SUBJECT_SANTA_ORGANIZER;

    /** @param string[] $available_verbs */
    public array $available_verbs = [self::VERB_AM, self::VERB_WANT_TO_BE];

    /** @param string[] $available_verbs */
    public array $available_subjects = [self::SUBJECT_SANTA_ORGANIZER, self::SUBJECT_GIFTEE];

    #[LiveAction]
    public function changeVerb(#[LiveArg] string $verb): void
    {
        $this->verb = $verb;
    }

    #[LiveAction]
    public function changeSubject(#[LiveArg] string $subject): void
    {
        $this->subject = $subject;
    }


    #[LiveAction]
    public function callToAction(): Response
    {
        return $this->redirectToRoute('app_index');
    }

    public function shouldLogin(): bool
    {
        return $this->verb === self::VERB_AM && $this->subject === self::SUBJECT_SANTA_ORGANIZER;
    }

    public function shouldCreateNewEvent(): bool
    {
        return $this->verb === self::VERB_WANT_TO_BE && $this->subject === self::SUBJECT_SANTA_ORGANIZER;
    }

    public function isGiftRecipient(): bool
    {
        return $this->verb === self::VERB_AM && $this->subject === self::SUBJECT_GIFTEE;
    }

    public function wantToReceiveGifts(): bool
    {
        return $this->verb === self::VERB_WANT_TO_BE && $this->subject === self::SUBJECT_GIFTEE;
    }
}
