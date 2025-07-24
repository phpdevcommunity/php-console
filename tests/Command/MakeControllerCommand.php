<?php

namespace Test\PhpDevCommunity\Console\Command;

use PhpDevCommunity\Console\Argument\CommandArgument;
use PhpDevCommunity\Console\Command\CommandInterface;
use PhpDevCommunity\Console\InputInterface;
use PhpDevCommunity\Console\Option\CommandOption;
use PhpDevCommunity\Console\OutputInterface;

class MakeControllerCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'make:controller';
    }

    public function getDescription(): string
    {
        return 'TEST : Make a new controller';
    }

    public function getOptions(): array
    {
        return [
        ];
    }

    public function getArguments(): array
    {
        return [
        ];
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->write('Test OK : Make a new controller');
    }
}
