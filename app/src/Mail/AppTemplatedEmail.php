<?php

namespace App\Mail;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AppTemplatedEmail extends TemplatedEmail
{
    abstract public function initialize(TranslatorInterface $translator): self;
}
