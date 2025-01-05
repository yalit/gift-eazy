<?php

use Castor\Attribute\AsRawTokens;
use Castor\Attribute\AsTask;
use Castor\Services\Docker;
use function Castor\io;


#[AsTask]
function console(#[AsRawTokens] array $arg): void
{
    io()->title("Executing a console command");
    Docker::exec(['bin/console', ...$arg]);
}
