<?php


use Castor\Attribute\AsTask;
use Castor\Services\Docker;
use function Castor\io;

#[AsTask(name: "ci:phpstan", description: "Runs phpstan on the project codebase")]
function phpstan(): void
{
    io()->title("Running phpstan");

    Docker::exec(['vendor/bin/phpstan', 'analyse']);
}

#[AsTask(name: "ci:composer", description: "Runs Composer validation")]
function composer(): void
{
    io()->title("Running Composer validation");

    Docker::exec(['composer', 'validate', '--strict']);
}

