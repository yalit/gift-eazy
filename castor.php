<?php

use Castor\Attribute\AsContext;
use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;

use Castor\Context;
use Castor\Services\Docker;
use function Castor\import;
use function Castor\io;
use function Castor\capture;
use function Castor\notify;
use function Castor\run;

import(__DIR__ . "/.castor");

#[AsContext(default: true)]
function project_context(): Context
{
    return new Context([
        'infraFolder' => __DIR__ . '/infra/',
        'appFolder' => __DIR__ . '/app/',
        'bashContainerName' => 'base',
        'userId' => capture('id -u'),
        'groupId' => capture('id -g')
    ]);
}


