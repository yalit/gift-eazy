<?php

namespace Castor\Services;

use Castor\Context;
use Symfony\Component\Process\Process;
use function Castor\context;
use function Castor\io;
use function Castor\run;
use function Castor\variable;

class Docker
{
    /**
     * @param string[]|null $files
     */
    public static function up(?array $files = null, bool $inBackground = true, bool $withBuild = false): Process
    {
        $commands = [];

        if (null === $files) {
            $files = self::getDefaultFiles();
        }

        foreach($files as $file) {
            $commands  = array_merge($commands, ['-f', $file]);
        }

        $commands = array_merge($commands, ['up']);

        if ($inBackground) {
            $commands = array_merge($commands, ['-d']);
        }

        if ($withBuild) {
            $commands = array_merge($commands, ['--build']);
        }

        return self::compose($commands);
    }


    /**
     * @param string[]|null $files
     */
    public static function down(?array $files = null): Process
    {
        $commands = [];

        if (null === $files) {
            $files = self::getDefaultFiles();
        }

        foreach($files as $file) {
            $commands  = array_merge($commands, ['-f', $file]);
        }

        $commands = array_merge($commands, ['down']);

        return self::compose($commands);
    }

    /**
     * @param string[]|null $files
     */
    public static function build(?array $files = null): Process
    {
        $commands = [];

        if (null === $files) {
            $files = self::getDefaultFiles();
        }

        foreach($files as $file) {
            $commands  = array_merge($commands, ['-f', $file]);
        }

        $commands = array_merge($commands, ['build']);

        return self::compose($commands);
    }

    /**
     * @param string[] $commands
     */
    public static function compose(array $commands = []): Process
    {
        $compose_command = ['docker', 'compose'];

        $full_command = array_merge($compose_command, $commands);

        return run($full_command, context: self::getDockerContext());
    }

    public static function exec(array $arguments, ?string $containerName = null): Process
    {
        $exec_command = ['docker', 'container', 'exec', '-it', $containerName ?? variable('bashContainerName')];

        $full_command = array_merge($exec_command, $arguments);

        return run($full_command, context: self::getDockerContext());
    }

    /**
     * @return string[]
     */
    private static function getDefaultFiles(): array
    {
        return [variable('infraFolder') . 'compose.yml'];
    }

    private static function getDockerContext(): Context
    {
        return context()->withEnvironment([
            'USER_ID' => variable("userId"),
            'GROUP_ID' => variable("groupId")
        ]);
    }
}
