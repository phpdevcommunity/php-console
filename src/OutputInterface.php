<?php

namespace PhpDevCommunity\Console;

interface OutputInterface
{
    public function write(string $message): void;
    public function writeln(string $message): void;
}
