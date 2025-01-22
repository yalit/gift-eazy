<?php

use Castor\Attribute\AsTask;
use Castor\Services\Docker;
use function Castor\capture;
use function Castor\io;
use function Castor\run;

#[AsTask(name:'test:all', description: "Run all the test available")]
function tests(string $testSuite = ""): void
{
    if ($testSuite === "") {
        io()->title("Running full test suite");
    } else {
        io()->title(sprintf("Running %s test suite", $testSuite));
    }

    $command = ['bin/phpunit', "--testdox"];

    if ($testSuite !== "") {
        $command = array_merge($command, ['--testsuite', $testSuite]);
    }

    Docker::exec($command);

}

#[AsTask(name: 'test:filter', description: "Run test filtering on input")]
function tests_filter(string $filter = ''): void
{
    io()->title(sprintf("Running filtered test with %s", $filter));
    $command = ['bin/phpunit', "--filter", $filter, '--testdox'];
    Docker::exec($command);
}

#[AsTask(name:'test:unit', description: "Run unit tests")]
function test_unit(): void
{
    tests("unit");
}

#[AsTask(name:'test:integration', description: "Run unit tests")]
function test_integration(): void
{
    tests("integration");
}

#[AsTask(name:'test:functional', description: "Run unit tests")]
function test_functional(): void
{
    tests("functional");
}

#[AsTask(name: "test:prepare", description: "Prepare the test environment")]
function prepareTestEnvironment(): void
{
    // drop DB if exists
    dbDrop("test", true);

    // create DB if exists
    dbCreate('test');

    // push migrations to test db
    dbMigrate('test', true);

    // load fixtures
    loadFixtures();
}

#[AsTask(name: "test:fixtures", description: "Load the fixtures in the test environment")]
function test_loadFixtures(): void
{
    loadFixtures('test');
}
