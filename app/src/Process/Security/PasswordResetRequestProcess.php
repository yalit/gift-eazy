<?php

namespace App\Process\Security;
use App\Entity\Security\Factory\PasswordResetRequestTokenFactory;
use App\Mail\HTMLEmailFactory;
use App\Mail\Security\PasswordResetRequestMail;
use App\Repository\Security\PasswordResetTokenRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class PasswordResetRequestProcess
{
    public function __construct(
        private PasswordResetTokenRepository $passwordResetTokenRepository,
        private HTMLEmailFactory $emailFactory,
        private MailerInterface $mailer,
        private string $notificationEmailSender
    ) {
    }

    public function __invoke(PasswordResetRequest $passwordResetRequest): void
    {
        $resetToken = PasswordResetRequestTokenFactory::create($passwordResetRequest->getEmail());
        // save the token
        $this->passwordResetTokenRepository->save($resetToken);

        // send request email with token
        $this->mailer->send($this->emailFactory->generate(
            PasswordResetRequestMail::class,
            $this->notificationEmailSender,
            $passwordResetRequest->getEmail(),
            [
                'resetToken' => $resetToken->getToken()
            ]
        ));
    }
}
