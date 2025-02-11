<?php

use Castor\Attribute\AsTask;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;
use function Castor\io;

#[AsTask(name:"db:migration", description:"Generates new migration for the Symfony application")]
function createMigration(?string $env = null): void
{
    io()->title("Generates new migration");

    $command = ['make:migration'];

    if (null !== $env) {
        $command = array_merge($command, ['--env='.$env]);
    }

    console($command);
}

#[AsTask(name:"db:migrate", description:"Push the migration to the DB")]
function dbMigrate(?string $env = null, bool $force = false): void
{
    io()->title("Generates new migration");

    $command = ['doctrine:migration:migrate'];

    if (null !== $env) {
        $command = array_merge($command, ['--env='.$env]);
    }

    if ($force) {
        $command = array_merge($command, ['--no-interaction']);
    }

    console($command);
}

#[AsTask(name:"db:create", description: "Create a new database")]
function dbCreate(?string $env = null, bool $force = false): void
{
    io()->title("Creates a new Database");

    $command = ['doctrine:database:create', '--if-not-exists'];

    if (null !== $env) {
        $command = array_merge($command, ['--env='.$env]);
    }

    console($command);
}

#[AsTask(name: "db:drop", description: "Drops the DB in the environment")]
function dbDrop(?string $env = null, bool $force = false): ?Process
{
    io()->title("Dropping Database");

    if (!$force) {
        $confirmed = io()->askQuestion(new Question("Do you confirm wanting to delete the database [yes|no]?", "no"));

        if ($confirmed !== 'yes') {
            return null;
        }
    }

    $command = ['doctrine:database:drop', '--force'];

    if (null !== $env) {
        $command = array_merge($command, ['--env='.$env]);
    }

    return console($command);
}

#[AsTask(name: "db:fixtures", description: "Load the fixtures in the test environment")]
function loadFixtures(string $env = "dev"): void
{
    io()->title(sprintf("Loading fixtures in %s environment", $env));

    console(["doctrine:fixtures:load", sprintf("--env=%s", $env), "--no-interaction"]);
}
