<?php

namespace Test\PhpDevCommunity\Console;

use PhpDevCommunity\Console\CommandParser;
use Depo\UniTester\TestCase;

class CommandParserTest extends TestCase
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
        $this->testCommandName();
        $this->testOptions();
        $this->testArguments();
        $this->testHasOption();
        $this->testGetOptionValue();
        $this->testGetArgumentValue();
    }

    public function testCommandName()
    {
        $parser = new CommandParser(self::createArgv(['foo', '--bar=baz']));
        $this->assertEquals('foo', $parser->getCommandName());
    }

    public function testOptions()
    {
        $parser = new CommandParser(self::createArgv(['foo', '--bar=baz', '--qux']));
        $this->assertEquals(['bar' => 'baz', 'qux' => true], $parser->getOptions());
    }

    public function testArguments()
    {
        $parser = new CommandParser(self::createArgv(['foo', 'bar', 'baz']));
        $this->assertEquals(['bar', 'baz'], $parser->getArguments());
    }

    public function testHasOption()
    {
        $parser = new CommandParser(self::createArgv(['foo', '--bar=baz']));
        $this->assertTrue($parser->hasOption('bar'));
        $this->assertFalse($parser->hasOption('qux'));
    }

    public function testGetOptionValue()
    {
        $parser = new CommandParser(self::createArgv(['foo', '--bar=baz']));
        $this->assertEquals('baz', $parser->getOptionValue('bar'));
    }

    public function testGetArgumentValue()
    {
        $parser = new CommandParser(self::createArgv(['foo', 'bar', 'baz']));
        $this->assertEquals('bar', $parser->getArgumentValue(0));
        $this->assertEquals('baz', $parser->getArgumentValue(1));
    }

    public function testHasArgument()
    {
        $parser = new CommandParser(self::createArgv(['foo', 'bar', 'baz']));
        $this->assertTrue($parser->hasArgument(0));
        $this->assertTrue($parser->hasArgument(1));
        $this->assertFalse($parser->hasArgument(2));
    }

    private static function createArgv(array $argv): array
    {
        return array_merge(['bin/console'], $argv);
    }
}
