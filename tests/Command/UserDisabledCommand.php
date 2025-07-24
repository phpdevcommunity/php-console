<?php

namespace Test\PhpDevCommunity\Console\Command;

use PhpDevCommunity\Console\Command\CommandInterface;
use PhpDevCommunity\Console\InputInterface;
use PhpDevCommunity\Console\OutputInterface;

class UserDisabledCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'app:user:disabled';
    }

    public function getDescription(): string
    {
        return 'TEST : User disabled';
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
        $output->write('Test OK : User disabled');
    }
}
