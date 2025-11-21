<?php

namespace Test\PhpDevCommunity\Console;

use PhpDevCommunity\Console\Output;
use Depo\UniTester\TestCase;
class OutputTest extends TestCase
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
        $this->testWrite();
        $this->testWriteln();
    }

    public function testWrite()
    {
        $output = new Output(function ($message) {
            $this->assertEquals('Hello, world!', $message);
        });

        $output->write('Hello, world!');
    }

    public function testWriteln()
    {
        $lines = 0;
        $output = new Output(function ($message) use(&$lines) {
            if ($lines === 0) {
                $this->assertEquals('Hello, world!', $message);
            }
            if ($lines === 1) {
                $this->assertEquals(PHP_EOL, $message);
            }
            $lines++;
        });

        $output->writeln('Hello, world!');
        $this->assertEquals(2, $lines);
    }
}
