<?php

namespace App\Process\Security;

use App\Mail\HTMLEmailFactory;
use App\Mail\Security\PasswordResetNotificationMail;
use App\Repository\Security\PasswordResetTokenRepository;
use App\Repository\Security\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
readonly class PasswordResetProcess
{
    public function __construct(
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private PasswordResetTokenRepository $passwordResetTokenRepository,
        private HTMLEmailFactory $HTMLEmailFactory,
        private MailerInterface $mailer,
        private string $notificationEmailSender
    ) {
    }

    public function __invoke(PasswordReset $passwordReset): void
    {
        // get the user from the email
        $user = $this->userRepository->findUserByMail($passwordReset->getEmail());

        // set a new password
        $this->userRepository->upgradePassword($user, $this->userPasswordHasher->hashPassword($user, $passwordReset->getPlainPassword()));

        //set the token to used
        $resetToken = $this->passwordResetTokenRepository->findTokenForEmailAndToken($passwordReset->getToken(), $passwordReset->getEmail());
        $resetToken->setUsed(true);
        $this->passwordResetTokenRepository->save($resetToken);

        $this->mailer->send(
            $this->HTMLEmailFactory->generate(PasswordResetNotificationMail::class, $this->notificationEmailSender, $user->getEmail(), [])
        );
    }
}
