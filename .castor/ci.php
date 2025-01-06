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
