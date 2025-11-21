<?php

namespace Test\PhpDevCommunity\Console;

use PhpDevCommunity\Console\Option\CommandOption;
use PhpDevCommunity\Console\Output;
use Depo\UniTester\TestCase;

class CommandOptionTest extends TestCase
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
        $this->testConstructor();
        $this->testGetName();
        $this->testGetShortcut();
        $this->testGetDescription();
        $this->testIsFlag();
    }

    public function testConstructor(): void
    {
        $option = new CommandOption('foo', 'f', 'description', true);

        $this->assertEquals('foo', $option->getName());
        $this->assertEquals('f', $option->getShortcut());
        $this->assertEquals('description', $option->getDescription());
        $this->assertTrue($option->isFlag());
    }

    public function testGetName(): void
    {
        $option = new CommandOption('foo');
        $this->assertEquals('foo', $option->getName());
    }

    public function testGetShortcut(): void
    {
        $option = new CommandOption('foo', 'f');
        $this->assertEquals('f', $option->getShortcut());
    }

    public function testGetDescription(): void
    {
        $option = new CommandOption('foo', null, 'description');
        $this->assertEquals('description', $option->getDescription());
    }

    public function testIsFlag(): void
    {
        $option = new CommandOption('foo', null, null, true);
        $this->assertTrue($option->isFlag());
    }

}
