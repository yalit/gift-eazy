<?php

namespace App\Mail\Security;

use App\Mail\AppTemplatedEmail;
use Symfony\Contracts\Translation\TranslatorInterface;

class PasswordResetNotificationMail extends AppTemplatedEmail
{
    public function initialize(TranslatorInterface $translator): AppTemplatedEmail
    {
        return $this->htmlTemplate('mail/security/password_reset_notification.html.twig')
            ->subject($translator->trans('mail.security.password_reset_notification.subject'))
            ;
    }
}
