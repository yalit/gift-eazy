<?php

namespace App\Tests\Functional\Security;

use App\DataFixtures\PasswordResetTokenFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Security\PasswordResetToken;
use App\Entity\Security\User;
use App\Repository\Security\PasswordResetTokenRepository;
use App\Repository\Security\UserRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PasswordResetControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TranslatorInterface $translator;
    private PasswordResetTokenRepository $resetRequestTokenRepository;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $this->translator = $container->get(TranslatorInterface::class);
        $this->resetRequestTokenRepository = $container->get(PasswordResetTokenRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->userPasswordHasher = $container->get(UserPasswordHasherInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->client);
        unset($this->translator);
        unset($this->resetRequestTokenRepository);
        unset($this->userRepository);
        unset($this->userPasswordHasher);
        self::ensureKernelShutdown();
    }

    public function testSuccessfulPasswordResetRequest(): void
    {
        $user = $this->userRepository->findUserByMail(UserFixtures::FIRST_USER_EMAIL);
        $resetToken = $this->resetRequestTokenRepository->findLastTokenForEmail(UserFixtures::FIRST_USER_EMAIL);
        self::assertNotNull($resetToken);

        $this->client->request('GET', '/password/reset/'.$resetToken->getToken());
        self::assertResponseIsSuccessful();

        $newPassword  ="New_Password123!";
        self::assertFalse($this->userPasswordHasher->isPasswordValid($user, $newPassword));

        $this->client->submitForm($this->translator->trans('pages.security.password_reset.form.submit'), [
            'password_reset[email]' => UserFixtures::FIRST_USER_EMAIL,
            'password_reset[plainPassword]' => $newPassword,
            'password_reset[token]' => $resetToken->getToken(),

        ]);

        self::assertResponseRedirects('/login');
        $this->client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertAnySelectorTextContains("div.notification", $this->translator->trans("pages.security.password_reset.notification.successful_reset"));

        $resetToken = $this->resetRequestTokenRepository->findTokenForEmailAndToken($resetToken->getToken(), UserFixtures::FIRST_USER_EMAIL);
        self::assertTrue($resetToken->isUsed());

        $user = $this->userRepository->findUserByMail(UserFixtures::FIRST_USER_EMAIL);
        self::assertTrue($this->userPasswordHasher->isPasswordValid($user, $newPassword));
    }

    public function testUnknownToken(): void
    {
        $this->client->request('GET', '/password/reset/unknown-token');
        self::assertResponseRedirects('/login');
        $this->client->followRedirect();
        self::assertResponseIsSuccessful();

        self::assertSelectorExists('div.notification');
    }

    public function testUsedToken(): void
    {
        $usedTokens = $this->resetRequestTokenRepository->findBy(['used' => true]);
        self::assertNotCount(0, $usedTokens);
        $usedToken = $usedTokens[0];

        $this->client->request('GET', '/password/reset/'.$usedToken->getToken());
        self::assertResponseRedirects('/login');
        $this->client->followRedirect();
        self::assertResponseIsSuccessful();

        self::assertSelectorExists('div.notification');
    }

    public function testExpiredToken(): void
    {
        $userTokens = $this->resetRequestTokenRepository->findBy(['email' => PasswordResetTokenFixtures::EXPIRED_TOKEN_EMAIL]);
        $expiredTokens = array_filter($userTokens, fn(PasswordResetToken $token) => $token->getExpirationDate() < new DateTimeImmutable());
        self::assertNotCount(0, $expiredTokens);
        $expiredToken = $expiredTokens[0];

        $this->client->request('GET', '/password/reset/'.$expiredToken->getToken());
        self::assertResponseRedirects('/login');
        $this->client->followRedirect();
        self::assertResponseIsSuccessful();

        self::assertSelectorExists('div.notification');
    }
}
