<?php


namespace PhpDevCommunity\Console\Command;


use PhpDevCommunity\Console\InputInterface;
use PhpDevCommunity\Console\Output;
use PhpDevCommunity\Console\OutputInterface;

class HelpCommand implements CommandInterface
{
    /**
     * @var CommandInterface[]
     */
    private array $commands;

    public function getName(): string
    {
        return 'help';
    }

    public function getDescription(): string
    {
        return 'Displays a list of available commands and their descriptions.';
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new Output\ConsoleOutput($output);
        $io->title('PhpDevCommunity Console - A PHP Console Application');

        $io->writeColor('Usage:', 'yellow');
        $io->write(\PHP_EOL);
        $io->write('  command [options] [arguments]');
        $io->write(\PHP_EOL);
        $io->write(\PHP_EOL);


        $io->writeColor('List of Available Commands:', 'yellow');
        $io->write(\PHP_EOL);
        $commands = [];
        foreach ($this->commands as $command) {
            $commands[$command->getName()] = $command->getDescription();
        }
        $io->listKeyValues($commands, true);
    }

    /**
     * @param CommandInterface[] $commands
     */
    public function setCommands(array $commands)
    {
        $this->commands = $commands;
    }

    public function getOptions(): array
    {
        return [];
    }

    public function getArguments(): array
    {
        return [];
    }
}
