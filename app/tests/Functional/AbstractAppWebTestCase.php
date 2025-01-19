<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractAppWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->client);
        unset($this->translator);
        self::ensureKernelShutdown();
    }
}
