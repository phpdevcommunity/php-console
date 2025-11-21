<?php

namespace PhpDevCommunity\Console;

interface OutputInterface
{
    public function write(string $message): void;
    public function writeln(string $message): void;
    public function setVerbose(bool $verbose): void;
    public function isVerbose(): bool;
}
