<?php

use Castor\Attribute\AsTask;
use Castor\Services\Docker;
use function Castor\io;

#[AsTask(name: "docker:up", description: 'Builds and starts the infrastructure', aliases: ['up'])]
function up(): void
{
    io()->title('Starting infrastructure');
    Docker::up();
}

#[AsTask(name: "docker:stop", description: 'Stops the infrastructure', aliases: ['stop'])]
function stop(): void
{
    io()->title('Stopping infrastructure');
    Docker::down();
}
#[AsTask(name: "docker:build", description: 'Stops the infrastructure', aliases: ['build'])]
function build(): void
{
    io()->title('Building infrastructure');
    Docker::build();
}

#[AsTask(name: "docker:bash", description: "Logs into the base container")]
function bash(): void
{
    io()->title('Login into the infrastructure');
    Docker::exec(['bash']);
}
