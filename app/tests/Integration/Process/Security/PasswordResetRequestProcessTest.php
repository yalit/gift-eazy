<?php

namespace App\Tests\Integration\Process\Security;

use App\Process\Security\PasswordResetRequest;
use App\Process\Security\PasswordResetRequestProcess;
use App\Repository\Security\PasswordResetTokenRepository;
use App\Repository\Security\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PasswordResetRequestProcessTest extends KernelTestCase
{
    private PasswordResetTokenRepository $passwordResetTokenRepository;
    private ValidatorInterface $validator;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->passwordResetTokenRepository = self::getContainer()->get(PasswordResetTokenRepository::class);
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    protected function tearDown(): void
    {
        unset($this->passwordResetTokenRepository);
        unset($this->validator);
        unset($this->userRepository);
    }

    public function testCorrectPasswordResetRequestContent(): void
    {
        $users = $this->userRepository->findAll();
        self::assertNotEmpty($users);
        $user = $users[0];

        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail($user->getEmail());

        $violations = $this->validator->validate($passwordResetRequest);

        self::assertCount(0, $violations);
    }

    public function testNotEmailRaiseViolationForPasswordResetRequest(): void
    {
        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail("test");

        $violations = $this->validator->validate($passwordResetRequest);

        self::assertCount(1, $violations);
    }

    public function testEmptyEmailRaiseViolationForPasswordResetRequest(): void
    {
        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail("");

        $violations = $this->validator->validate($passwordResetRequest);

        self::assertCount(1, $violations);
    }

    public function testNotAUserEmailRaiseViolationForPasswordResetRequest(): void
    {
        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail("not_a_user@email.com");

        $violations = $this->validator->validate($passwordResetRequest);

        self::assertCount(1, $violations);
    }

    public function testSuccessfulPasswordResetRequest(): void
    {
        $process = new PasswordResetRequestProcess($this->passwordResetTokenRepository);

        $email = "password_reset_request_test@email.com";
        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail($email);

        $allTokens = $this->passwordResetTokenRepository->findAll();

        $process($passwordResetRequest);

        $newAllTokens = $this->passwordResetTokenRepository->findAll();

        self::assertCount(count($allTokens) + 1, $newAllTokens);
    }
}
