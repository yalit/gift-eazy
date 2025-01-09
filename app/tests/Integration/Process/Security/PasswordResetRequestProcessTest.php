<?php

namespace App\Tests\Integration\Process\Security;

use App\Mail\HTMLEmailFactory;
use App\Process\Security\PasswordResetRequest;
use App\Process\Security\PasswordResetRequestProcess;
use App\Repository\Security\PasswordResetTokenRepository;
use App\Repository\Security\UserRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PasswordResetRequestProcessTest extends KernelTestCase
{
    private PasswordResetTokenRepository $passwordResetTokenRepository;
    private ValidatorInterface $validator;
    private UserRepository $userRepository;
    private TranslatorInterface $translator;
    private RouterInterface $router;

    protected function setUp(): void
    {
        $this->passwordResetTokenRepository = self::getContainer()->get(PasswordResetTokenRepository::class);
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
        $this->router = static::getContainer()->get(RouterInterface::class);
    }

    protected function tearDown(): void
    {
        unset($this->passwordResetTokenRepository);
        unset($this->validator);
        unset($this->userRepository);
        unset($this->translator);
        unset($this->router);
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
        $process = new PasswordResetRequestProcess(
            $this->passwordResetTokenRepository,
            self::getContainer()->get(HTMLEmailFactory::class),
            self::getContainer()->get(MailerInterface::class),
            "sender@notification.com"
        );

        $email = "password_reset_request_test@email.com";
        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail($email);

        $allTokens = $this->passwordResetTokenRepository->findAll();

        $process($passwordResetRequest);

        $newAllTokens = $this->passwordResetTokenRepository->findAll();
        self::assertCount(count($allTokens) + 1, $newAllTokens);

        $resetToken = $this->passwordResetTokenRepository->findLastTokenForEmail($email);
        self::assertNotNull($resetToken);
        self::assertFalse($resetToken->isUsed());
        self::assertGreaterThan( new DateTimeImmutable() ,$resetToken->getExpirationDate());

        self::assertQueuedEmailCount(1);
        $emailSent = self::getMailerMessage();
        self::assertEmailSubjectContains($emailSent, $this->translator->trans('mail.security.password_reset_request.subject') );
        self::assertEmailAddressContains($emailSent, "To",$email);
        self::assertEmailHtmlBodyContains($emailSent, $this->router->generate('security_password_reset', ['token' => $resetToken->getToken()]));
    }
}
