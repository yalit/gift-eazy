<?php

use Castor\Attribute\AsRawTokens;
use Castor\Attribute\AsTask;
use Castor\Services\Docker;
use function Castor\io;


#[AsTask(name:"composer:require", description: "Require a new composer module")]
function composer_require(#[AsRawTokens] array $arg, bool $dev = false): void
{
    io()->text("Requiring a composer module");
    $command = ['composer', 'require', ...$arg ];

    if ($dev) {
        $command = array_merge($command, ['--dev']);
    }

    Docker::exec($command);
}

#[AsTask(name:"composer:install", description: "Install the composer modules")]
function composer_install(): void
{
    io()->text("Installing all composers dependencies");
    $command = ['composer', 'install'];

    Docker::exec($command);
}
