<?php

use Castor\Attribute\AsRawTokens;
use Castor\Attribute\AsTask;
use Castor\Services\Docker;
use Symfony\Component\Process\Process;
use function Castor\io;


#[AsTask]
function console(#[AsRawTokens] array $arg): Process
{
    io()->text("Executing a console command : ". join(" ", ['bin/console', ...$arg]));
    return Docker::exec(['bin/console', ...$arg]);
}
