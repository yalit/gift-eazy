<?php

namespace App\Tests\Functional\Security;

use App\DataFixtures\UserFixtures;
use App\Entity\Security\User;
use App\Repository\Security\PasswordResetTokenRepository;
use App\Repository\Security\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class PasswordResetRequestControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $userRepository;
    private TranslatorInterface $translator;
    private PasswordResetTokenRepository $resetRequestTokenRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $this->userRepository = $em->getRepository(User::class);
        $this->translator = $container->get(TranslatorInterface::class);
        $this->resetRequestTokenRepository = $container->get(PasswordResetTokenRepository::class);
    }

    protected function tearDown(): void
    {
        unset($this->client);
        unset($this->userRepository);
        unset($this->translator);
        unset($this->resetRequestTokenRepository);
    }

    public function testSuccessfulPasswordResetRequest(): void
    {
        $this->client->request('GET', '/password/reset/request');
        self::assertResponseIsSuccessful();

        $user = $this->userRepository->findAll()[0];
        self::assertNotNull($user);

        $allTokens = $this->resetRequestTokenRepository->findAll();

        $this->client->submitForm($this->translator->trans('pages.security.password_reset_request.form.submit'), [
            'password_reset_request[email]' => $user->getEmail(),
        ]);

        self::assertResponseRedirects('/password/reset/request/confirmed');
        $this->client->followRedirect();
        self::assertResponseIsSuccessful();

        $newAllTokens = $this->resetRequestTokenRepository->findAll();
        self::assertCount(count($allTokens) + 1, $newAllTokens);
    }
}
