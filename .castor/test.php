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

    Docker::exec(['bin/phpunit', "--testdox"]);

}

#[AsTask(name: "test:prepare", description: "Prepare the test environment")]
function prepareTestEnvironment(): void
{
    // drop DB if exists
    dbDrop("test", true);

    // create DB if exists
    dbCreate('test');

    // push migrations to test db
    dbMigrate('test');

    // load fixtures
    loadFixtures();
}

#[AsTask(name: "test:fixtures", description: "Load the fixtures in the test environment")]
function loadFixtures(): void
{
    io()->title("Loading fixtures in test environment");

    console(["doctrine:fixtures:load", "--env=test", "--no-interaction"]);
}
