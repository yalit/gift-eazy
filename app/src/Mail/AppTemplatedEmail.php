<?php

namespace App\Mail;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AppTemplatedEmail extends TemplatedEmail
{
    /**
     * @param array<string|int|object> $context
     */
    abstract public function initialize(TranslatorInterface $translator, array $context): self;
}
