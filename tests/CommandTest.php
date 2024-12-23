<?php

namespace Test\PhpDevCommunity\Console;

use PhpDevCommunity\Console\Argument\CommandArgument;
use PhpDevCommunity\Console\Input;
use PhpDevCommunity\Console\Option\CommandOption;
use PhpDevCommunity\Console\Output;
use PhpDevCommunity\UniTester\TestCase;
use Test\PhpDevCommunity\Console\Command\FooCommand;

class CommandTest extends TestCase
{

    protected function setUp(): void
    {
        // TODO: Implement setUp() method.
    }

    protected function tearDown(): void
    {
        // TODO: Implement tearDown() method.
    }

    protected function execute(): void
    {
        $this->testGetName();
        $this->testGetDescription();
        $this->testGetOptions();
        $this->testGetArguments();
        $this->testExecute();
    }

    public function testGetName(): void
    {
        $command = new FooCommand();
        $this->assertEquals('foo', $command->getName());
    }

    public function testGetDescription(): void
    {
        $command = new FooCommand();
        $this->assertEquals('Performs the foo operation with optional parameters.', $command->getDescription());
    }

    public function testGetOptions(): void
    {
        $command = new FooCommand();
        $options = $command->getOptions();
        $this->assertEquals(2, count($options));

        $this->assertInstanceOf(CommandOption::class, $options[0]);
        $this->assertEquals('verbose', $options[0]->getName());
        $this->assertEquals('v', $options[0]->getShortcut());
        $this->assertEquals('Enable verbose output', $options[0]->getDescription());
        $this->assertTrue($options[0]->isFlag());

        $this->assertInstanceOf(CommandOption::class, $options[1]);
        $this->assertEquals('output', $options[1]->getName());
        $this->assertEquals('o', $options[1]->getShortcut());
        $this->assertEquals('Specify output file', $options[1]->getDescription());
        $this->assertFalse($options[1]->isFlag());
    }

    public function testGetArguments(): void
    {
        $command = new FooCommand();
        $arguments = $command->getArguments();
        $this->assertEquals(1, count($arguments));
        $this->assertInstanceOf(CommandArgument::class, $arguments[0]);
        $this->assertEquals('input', $arguments[0]->getName());
        $this->assertFalse($arguments[0]->isRequired());
    }

    public function testExecute(): void
    {
        $input = new Input('foo', ['verbose' => true, 'output' => 'output.txt'], ['input' => 'foo']);
        $lines = 0;
        $output = new Output(function (string $message) use (&$lines) {
            if ($lines === 0) {
                $this->assertEquals('Test OK', $message);
            }
            if ($lines === 2) {
                $this->assertEquals('ARGUMENTS: {"input":"foo"}', $message);
            }
            if ($lines === 4) {
                $this->assertEquals('OPTIONS: {"verbose":true,"output":"output.txt"}', $message);
            }
            $lines++;
        });
        $command = new FooCommand();
        $command->execute($input, $output);

        $this->assertEquals(6, $lines);
    }
}