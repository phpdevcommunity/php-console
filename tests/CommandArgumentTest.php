<?php

namespace Test\PhpDevCommunity\Console;

use PhpDevCommunity\Console\Argument\CommandArgument;
use PhpDevCommunity\Console\Output;
use PhpDevCommunity\UniTester\TestCase;

class CommandArgumentTest extends TestCase
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
        $this->testValidateThrowsExceptionIfRequiredAndValueIsEmpty();
        $this->testValidateDoesNotThrowExceptionIfNotRequiredAndValueIsEmpty();
        $this->testValidateDoesNotThrowExceptionIfRequiredAndValueIsNotEmpty();
        $this->testGetNameReturnsCorrectValue();
        $this->testIsRequiredReturnsCorrectValue();
        $this->testGetDefaultValueReturnsCorrectValue();
        $this->testGetDescriptionReturnsCorrectValue();
    }

    public function testValidateThrowsExceptionIfRequiredAndValueIsEmpty()
    {
        $arg = new CommandArgument('test', true);

        $this->expectException(\InvalidArgumentException::class, function () use ($arg) {
            $arg->validate('');
        }, 'The required argument "test" was not provided.');

    }

    public function testValidateDoesNotThrowExceptionIfNotRequiredAndValueIsEmpty()
    {
        $arg = new CommandArgument('test');

        $arg->validate('');

        $this->assertTrue(true);
    }

    public function testValidateDoesNotThrowExceptionIfRequiredAndValueIsNotEmpty()
    {
        $arg = new CommandArgument('test', true);

        $arg->validate('value');

        $this->assertTrue(true);
    }

    public function testGetNameReturnsCorrectValue()
    {
        $arg = new CommandArgument('test');

        $this->assertEquals('test', $arg->getName());
    }

    public function testIsRequiredReturnsCorrectValue()
    {
        $arg = new CommandArgument('test', true);

        $this->assertTrue($arg->isRequired());

        $arg = new CommandArgument('test');

        $this->assertFalse($arg->isRequired());
    }

    public function testGetDefaultValueReturnsCorrectValue()
    {
        $arg = new CommandArgument('test', false, 'default');

        $this->assertEquals('default', $arg->getDefaultValue());
    }

    public function testGetDescriptionReturnsCorrectValue()
    {
        $arg = new CommandArgument('test', false, null, 'description');

        $this->assertEquals('description', $arg->getDescription());
    }

}