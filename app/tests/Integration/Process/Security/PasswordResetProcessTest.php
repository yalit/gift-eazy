<?php

namespace App\Tests\Integration\Process\Security;

use App\DataFixtures\PasswordResetTokenFixtures;
use App\Mail\HTMLEmailFactory;
use App\Process\Security\PasswordReset;
use App\Process\Security\PasswordResetProcess;
use App\Repository\Security\PasswordResetTokenRepository;
use App\Repository\Security\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function PHPUnit\Framework\assertTrue;

class PasswordResetProcessTest extends KernelTestCase
{
    private const NEW_PASSWORD = "NEWPassword9753%_";
    private PasswordResetTokenRepository $passwordResetTokenRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private UserRepository $userRepository;
    private ValidatorInterface $validator;
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->passwordResetTokenRepository = self::getContainer()->get(PasswordResetTokenRepository::class);
        $this->userPasswordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
        $this->translator = self::getContainer()->get(TranslatorInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->passwordResetTokenRepository);
        unset($this->userPasswordHasher);
        unset($this->userRepository);
        unset($this->validator);
        unset($this->translator);
    }

    public function testCorrectPasswordReset(): void
    {
        $users = $this->userRepository->findAll();
        self::assertNotEmpty($users);
        $user = $users[0];

        $resetToken = $this->passwordResetTokenRepository->findLastTokenForEmail($user->getEmail());
        self::assertNotNull($resetToken);

        $passwordReset = $this->getPasswordReset($resetToken->getToken(), $resetToken->getEmail(), self::NEW_PASSWORD);
        $violations = $this->validator->validate($passwordReset);

        self::assertCount(0, $violations);
    }

    /**
     * @dataProvider getNotStrongEnoughPasswords
     */
    public function testNotStrongEnoughPasswordInPasswordReset(string $incorrectPassword): void
    {
        $users = $this->userRepository->findAll();
        self::assertNotEmpty($users);
        $user = $users[0];

        $resetToken = $this->passwordResetTokenRepository->findOneBy(['email' => $user->getEmail(), 'used' => false]);
        self::assertNotNull($resetToken);

        $passwordReset = $this->getPasswordReset($resetToken->getToken(), $resetToken->getEmail(), $incorrectPassword);
        $violations = $this->validator->validate($passwordReset);

        self::assertCount(1, $violations);
    }

    /**
     * @return iterable<string, array<string>>
     */
    public function getNotStrongEnoughPasswords(): iterable
    {
        yield "Empty Password" => [""];
        yield "No Uppercase" => ["onlyl123$"];
        yield "Too small" => ["Sm1ll$"];
        yield "No special Characters" => ["WITHUp13"];
    }

    /**
     * @dataProvider getIncorrectCombination
     */
    public function testNotACorrectTokenEmailCombinationInPasswordReset(bool $correctToken, string $email): void
    {
        $users = $this->userRepository->findAll();
        self::assertNotEmpty($users);
        $user = $users[0];

        $resetToken = $this->passwordResetTokenRepository->findOneBy(['email' => $user->getEmail(), 'used' => false]);
        self::assertNotNull($resetToken);

        $passwordReset = $this->getPasswordReset(
            $correctToken ? $resetToken->getToken() : Uuid::v4(),
            $email === "user" ? $user->getEmail() : $email,
            self::NEW_PASSWORD
        );
        $violations = $this->validator->validate($passwordReset);

        self::assertCount($correctToken ? 1 : 2, $violations); // Correct Pairing && ValidToken
    }


    /**
     * @return iterable<string, array<bool|string>>
     */
    public function getIncorrectCombination(): iterable
    {
        yield "Correct Token and Empty email" => [true, ""];
        yield "Correct Token and Not an email" => [true, "test"];
        yield "Correct Token and incorrect email" => [true, "incorrect_email@email.com"];
        yield "Unknown Token" => [false, "user"];
    }


    public function testSuccessfulPasswordReset(): void
    {
        $users = $this->userRepository->findAll();
        self::assertNotEmpty($users);
        $user = $users[0];

        $resetToken = $this->passwordResetTokenRepository->findOneBy(['email' => $user->getEmail(), 'used' => false]);
        self::assertNotNull($resetToken);

        $passwordReset = $this->getPasswordReset($resetToken->getToken(), $resetToken->getEmail(), self::NEW_PASSWORD);

        self::assertFalse($this->userPasswordHasher->isPasswordValid($user, self::NEW_PASSWORD));

        $process = new PasswordResetProcess(
            $this->userRepository,
            $this->userPasswordHasher,
            $this->passwordResetTokenRepository,
            self::getContainer()->get(HTMLEmailFactory::class),
            self::getContainer()->get(MailerInterface::class),
            'notification_test@email.com'
        );
        $process($passwordReset);

        self::assertTrue($this->userPasswordHasher->isPasswordValid($user, self::NEW_PASSWORD));
        assertTrue($resetToken->isUsed());

        self::assertQueuedEmailCount(1);
        $emailSent = self::getMailerMessage();
        self::assertEmailSubjectContains($emailSent, $this->translator->trans('mail.security.password_reset_notification.subject'));
    }

    public function getPasswordReset(string $token, string $email, string $password): PasswordReset
    {
        $passwordReset = new PasswordReset();
        $passwordReset->setEmail($email);
        $passwordReset->setToken($token);
        $passwordReset->setPlainPassword($password);
        return $passwordReset;
    }
}
