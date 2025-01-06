<?php

use Castor\Attribute\AsTask;
use Castor\Services\Docker;
use function Castor\capture;
use function Castor\io;
use function Castor\run;

#[AsTask(name:'test:all', description: "Run all the test available")]
function tests(): void
{
    io()->title("Running full test suite");

    Docker::exec(['bin/phpunit']);

}

#[AsTask(name: "test:prepare", description: "Prepare the test environment")]
function prepareTestEnvironment(): void
{
    // create DB if exists
    dbCreate('test');

    // push migrations to test db
    dbMigrate('test');
}
