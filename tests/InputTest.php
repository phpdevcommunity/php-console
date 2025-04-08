<?php

namespace Test\PhpDevCommunity\Console;

use PhpDevCommunity\Console\Input;
use PhpDevCommunity\Console\Output;
use PhpDevCommunity\UniTester\TestCase;

class InputTest extends TestCase
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
        $this->testGetCommandName();
        $this->testGetOptions();
        $this->testGetArguments();
        $this->testHasOption();
        $this->testHasArgument();
        $this->testGetOptionValue();
        $this->testGetArgumentValue();

    }

    public function testGetCommandName()
    {
        $input = new Input('test', [], []);
        $this->assertEquals('test', $input->getCommandName());
    }

    public function testGetOptions()
    {
        $options = ['--option1' => 'value1', '--option2' => 'value2'];
        $input = new Input('test', $options, []);
        $this->assertEquals($options, $input->getOptions());
    }

    public function testGetArguments()
    {
        $arguments = ['argument1' => 'value1', 'argument2' => 'value2'];
        $input = new Input('test', [], $arguments);
        $this->assertEquals($arguments, $input->getArguments());
    }

    public function testHasOption()
    {
        $input = new Input('test', ['--option' => 'value'], []);
        $this->assertTrue($input->hasOption('option'));
        $this->assertTrue($input->hasOption('--option'));
        $this->assertFalse($input->hasOption('invalid'));
    }

    public function testGetOptionValue()
    {
        $input = new Input('test', ['--option' => 'value'], []);
        $this->assertEquals('value', $input->getOptionValue('option'));
        $this->assertEquals('value', $input->getOptionValue('--option'));
        $this->expectException(\InvalidArgumentException::class, function () use ($input) {
            $input->getOptionValue('invalid');
        });
    }

    public function testGetArgumentValue()
    {
        $input = new Input('test', [], ['--argument' => 'value']);
        $this->assertEquals('value', $input->getArgumentValue('argument'));
        $this->expectException(\InvalidArgumentException::class, function () use ($input) {
            $input->getArgumentValue('invalid');
        });
    }

    public function testHasArgument()
    {
        $input = new Input('test', [], ['--argument' => 'value']);
        $this->assertTrue($input->hasArgument('argument'));
        $this->assertFalse($input->hasArgument('invalid'));
    }
}
