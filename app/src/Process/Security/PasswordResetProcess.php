<?php

namespace App\Process\Security;

use App\Repository\Security\PasswordResetTokenRepository;
use App\Repository\Security\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
readonly class PasswordResetProcess
{
    public function __construct(
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private PasswordResetTokenRepository $passwordResetTokenRepository,
    ) {
    }

    public function __invoke(PasswordReset $passwordReset): void
    {
        // get the user from the email
        $user = $this->userRepository->findUserByMail($passwordReset->getEmail());

        // set a new password
        $this->userRepository->upgradePassword($user, $this->userPasswordHasher->hashPassword($user, $passwordReset->getPlainPassword()));

        //set the token to used
        $resetToken = $this->passwordResetTokenRepository->findTokenForEmail($passwordReset->getToken(), $passwordReset->getEmail());
        $resetToken->setUsed(true);
        $this->passwordResetTokenRepository->save($resetToken);
    }
}
