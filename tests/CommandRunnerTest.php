<?php

namespace Test\PhpDevCommunity\Console;

use PhpDevCommunity\Console\CommandParser;
use PhpDevCommunity\Console\CommandRunner;
use PhpDevCommunity\Console\Output;
use PhpDevCommunity\UniTester\TestCase;
use Test\PhpDevCommunity\Console\Command\CacheClearCommand;
use Test\PhpDevCommunity\Console\Command\MakeControllerCommand;
use Test\PhpDevCommunity\Console\Command\MakeEntityCommand;
use Test\PhpDevCommunity\Console\Command\UserCreateCommand;
use Test\PhpDevCommunity\Console\Command\UserDisabledCommand;
use Test\PhpDevCommunity\Console\Command\UserListCommand;
use Test\PhpDevCommunity\Console\Command\UserResetPasswordCommand;

class CommandRunnerTest extends TestCase
{
    private CommandRunner $commandRunner;

    protected function setUp(): void
    {
        $this->commandRunner = new CommandRunner([
            new CacheClearCommand(),
            new MakeControllerCommand(),
            new MakeEntityCommand(),
            new UserCreateCommand(),
            new UserDisabledCommand(),
            new UserResetPasswordCommand(),
            new UserListCommand(),
        ]);
    }

    protected function tearDown(): void
    {
        // TODO: Implement tearDown() method.
    }

    protected function execute(): void
    {
        $exitCode = $this->commandRunner->run(new CommandParser(self::createArgv(['c:c'])), new Output(function ($message) {
            $this->assertEquals('Test OK : Clear cache', $message);
            })
        );
        $this->assertEquals(0, $exitCode);

        $message = '';
        $exitCode = $this->commandRunner->run(new CommandParser(self::createArgv(['c:c', '--verbose'])), new Output(function ($outputMessage) use (&$message) {
                $message .= $outputMessage;
            })
        );
        $this->assertStringContains( $message, 'Test OK : Clear cache');
        $this->assertStringContains( $message, 'Execution time:');
        $this->assertStringContains( $message, 'Peak memory usage:');
        $this->assertEquals(0, $exitCode);
    }

    private static function createArgv(array $argv): array
    {
        return array_merge(['bin/console'], $argv);
    }
}