<?php

namespace PhpDevCommunity\Console;

use RuntimeException;
use const PHP_EOL;

final class Output implements OutputInterface
{
    /**
     * @var callable
     */
    private $output;

    public function __construct(callable $output = null)
    {
        if ($output === null) {
            $output = function ($message) {
                fwrite(STDOUT, $message);
            };
        }
        $this->output = $output;
    }

    public function write(string $message): void
    {
        $output = $this->output;
        $output($message);
    }

    public function writeln(string $message): void
    {
        $this->write($message);
        $this->write(PHP_EOL);
    }
}
