<?php

namespace App\Mail;

use Symfony\Contracts\Translation\TranslatorInterface;

readonly class HTMLEmailFactory
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    /**
     * @param class-string $mailClass
     * @param string|string[] $sender
     * @param string|string[] $recipients
     * @param array<string|int|object> $parameters
     * @return AppTemplatedEmail
     */
    public function generate(
        string $mailClass,
        string|array $sender,
        string|array $recipients,
        array $parameters
    ): AppTemplatedEmail
    {
        /** @var AppTemplatedEmail $email */
        $email = new $mailClass();
        $email->from($sender)
            ->to($recipients)
            ->initialize($this->translator, $parameters)
        ;


        return $email;
    }
}
