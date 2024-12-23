<?php

namespace Test\PhpDevCommunity\Console\Command;

use PhpDevCommunity\Console\Argument\CommandArgument;
use PhpDevCommunity\Console\Command\CommandInterface;
use PhpDevCommunity\Console\InputInterface;
use PhpDevCommunity\Console\Option\CommandOption;
use PhpDevCommunity\Console\OutputInterface;

class FooCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'foo';
    }

    public function getDescription(): string
    {
        return 'Performs the foo operation with optional parameters.';
    }

    public function getOptions(): array
    {
        return [
            new CommandOption('verbose', 'v', 'Enable verbose output', true),
            new CommandOption('output', 'o', 'Specify output file', false)
        ];
    }

    public function getArguments(): array
    {
        return [
            new CommandArgument('input', false, null, 'The input file for the foo operation')
        ];
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {

        $output->writeln('Test OK');
        $output->writeln('ARGUMENTS: ' . json_encode($input->getArguments()));
        $output->writeln('OPTIONS: ' . json_encode($input->getOptions()));
    }
}
