<?php

namespace PhpDevCommunity\Console;

interface InputInterface
{
    public function getCommandName(): ?string;
    public function getOptions(): array;
    public function getArguments(): array;
    public function hasOption(string $name): bool;
    public function getOptionValue(string $name);
    public function getArgumentValue(string $name);
    public function hasArgument(string $name): bool;
}
