<?php

namespace App\Mail\Security;

use App\Mail\AppTemplatedEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Contracts\Translation\TranslatorInterface;

class PasswordResetRequestMail extends AppTemplatedEmail
{
    public function initialize(TranslatorInterface $translator): AppTemplatedEmail
    {
        return $this->htmlTemplate('mail/security/password_reset_request.html.twig')
            ->subject($translator->trans('mail.security.password_reset_request.subject'))
            ;
    }
}
