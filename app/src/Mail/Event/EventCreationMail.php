<?php

namespace App\Mail\Event;

use App\Mail\AppTemplatedEmail;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventCreationMail extends AppTemplatedEmail
{
    public function initialize(TranslatorInterface $translator): AppTemplatedEmail
    {
        return $this->htmlTemplate('mail/event/event_creation_email.html.twig')
            ->subject($translator->trans('mail.event.creation.subject', ['%name%' => $this->getContext()['name']]))
            ;
    }
}
