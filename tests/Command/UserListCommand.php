<?php

namespace Test\PhpDevCommunity\Console\Command;

use PhpDevCommunity\Console\Command\CommandInterface;
use PhpDevCommunity\Console\InputInterface;
use PhpDevCommunity\Console\Option\CommandOption;
use PhpDevCommunity\Console\OutputInterface;

class UserListCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'app:user:list';
    }

    public function getDescription(): string
    {
        return 'TEST : User list';
    }

    public function getOptions(): array
    {
        return [
            CommandOption::withValue('limit', 'l', 'Limit the number of results', 100),
        ];
    }

    public function getArguments(): array
    {
        return [
        ];
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->write('Test OK : User list');
        $output->write(sprintf('LIMIT : %d', $input->getOptionValue('limit')));
    }
}
