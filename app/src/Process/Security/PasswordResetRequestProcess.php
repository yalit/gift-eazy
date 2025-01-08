<?php

namespace App\Process\Security;
use App\Entity\Security\Factory\PasswordResetRequestTokenFactory;
use App\Repository\Security\PasswordResetTokenRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class PasswordResetRequestProcess
{
    public function __construct(
        private PasswordResetTokenRepository $passwordResetTokenRepository,
    ) {
    }

    public function __invoke(PasswordResetRequest $passwordResetRequest): void
    {
        $resetToken = PasswordResetRequestTokenFactory::create($passwordResetRequest->getEmail());
        // save the token
        $this->passwordResetTokenRepository->save($resetToken);


    }
}
