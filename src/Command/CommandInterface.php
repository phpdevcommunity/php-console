<?php

namespace PhpDevCommunity\Console\Command;

use PhpDevCommunity\Console\Argument\CommandArgument;
use PhpDevCommunity\Console\InputInterface;
use PhpDevCommunity\Console\Option\CommandOption;
use PhpDevCommunity\Console\OutputInterface;

interface CommandInterface
{
    /**
     * Returns the name of the command.
     *
     * @return string The name of the command.
     */
    public function getName(): string;

    /**
     * Returns the description of the command.
     *
     * @return string The description of the command.
     */
    public function getDescription(): string;

    /**
     * Returns the list of available options for the command.
     *
     * @return array<CommandOption> An array of CommandOption.
     */
    public function getOptions(): array;

    /**
     * Returns the list of required arguments for the command.
     *
     * @return array<CommandArgument> An array of CommandArgument.
     */
    public function getArguments(): array;

    /**
     * Executes the command with the provided inputs.
     *
     * @param InputInterface $input The inputs for the command.
     * @param OutputInterface $output
     * @return void
     * @throws \InvalidArgumentException If arguments or options are invalid.
     * @throws \RuntimeException If an error occurs during execution.
     */
    public function execute(InputInterface $input, OutputInterface $output): void;
}
