<?php

namespace Test\PhpDevCommunity\Console\Command;

use PhpDevCommunity\Console\Command\CommandInterface;
use PhpDevCommunity\Console\InputInterface;
use PhpDevCommunity\Console\OutputInterface;

class UserResetPasswordCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'app:user:reset-password';
    }

    public function getDescription(): string
    {
        return 'TEST : User reset password';
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
        $output->write('Test OK : User reset password');
    }
}
