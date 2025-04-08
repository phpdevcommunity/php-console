<?php

namespace PhpDevCommunity\Console;

use InvalidArgumentException;
use PhpDevCommunity\Console\Command\CommandInterface;
use PhpDevCommunity\Console\Command\HelpCommand;
use PhpDevCommunity\Console\Output\ConsoleOutput;
use Throwable;
use const PHP_EOL;

final class CommandRunner
{
    const CLI_ERROR = 1;
    const CLI_SUCCESS = 0;

    /**
     * @var CommandInterface[]
     */
    private array $commands = [];

    /**
     * @var CommandInterface
     */
    private CommandInterface $defaultCommand;

    /**
     * Application constructor.
     * @param CommandInterface[] $commands
     */
    public function __construct(array $commands)
    {
        $this->defaultCommand = new HelpCommand();
        foreach ($commands as $command) {
            if (!is_subclass_of($command, CommandInterface::class)) {
                $commandName = is_object($command) ? get_class($command) : $command;
                throw new InvalidArgumentException(sprintf('Command "%s" must implement "%s".', $commandName, CommandInterface::class));
            }
        }
        $this->commands = array_merge($commands, [$this->defaultCommand]);
        $this->defaultCommand->setCommands($this->commands);
    }

    public function run(CommandParser $commandParser, OutputInterface $output): int
    {
        try {

            if ($commandParser->getCommandName() === null || $commandParser->getCommandName() === '--help') {
                $this->defaultCommand->execute(new Input($this->defaultCommand->getName(), [], []), $output);
                return self::CLI_SUCCESS;
            }

            $commands = [];
            foreach ($this->commands as $currentCommand) {
                if (self::stringStartsWith($currentCommand->getName(), $commandParser->getCommandName())) {
                    $commands[] = $currentCommand;
                }
            }

            if (empty($commands)) {
                throw new InvalidArgumentException(sprintf('Command "%s" is not defined.', $commandParser->getCommandName()));
            }

            if (count($commands) > 1) {
                $names = [];
                foreach ($commands as $command) {
                    $names[$command->getName()] = $command->getDescription();
                }
                $consoleOutput = new ConsoleOutput($output);
                $consoleOutput->error(sprintf('Command "%s" is ambiguous.', $commandParser->getCommandName()));
                $consoleOutput->listKeyValues($names, true);
                return self::CLI_ERROR;
            }

            $command = $commands[0];
            if ($commandParser->hasOption('help')) {
                $this->showCommandHelp($command, $output);
                return self::CLI_SUCCESS;
            }

            $this->execute($command, $commandParser, $output);

            return self::CLI_SUCCESS;

        } catch (Throwable $e) {
            (new ConsoleOutput($output))->error($e->getMessage());
            return self::CLI_ERROR;
        }

    }

    private function execute(CommandInterface $command, CommandParser $commandParser, OutputInterface $output)
    {
        $argvOptions = [];

        $options = $command->getOptions();
        foreach ($options as $option) {
            if ($option->isFlag()) {
                $argvOptions["--{$option->getName()}"] = false;
            }
        }
        foreach ($commandParser->getOptions() as $name => $value) {
            $hasOption = false;
            foreach ($options as $option) {
                if ($option->getName() === $name || $option->getShortcut() === $name) {
                    $hasOption = true;
                    if (!$option->isFlag() && ($value === true || empty($value))) {
                        throw new InvalidArgumentException(sprintf('Option "%s" requires a value for command "%s".', $name, $command->getName()));
                    }
                    $argvOptions["--{$option->getName()}"] = $value;
                    break;
                }
            }
            if (!$hasOption) {
                throw new InvalidArgumentException(sprintf('Option "%s" is not defined for command "%s".', $name, $command->getName()));
            }
        }

        $argv = [];

        $arguments = $command->getArguments();
        foreach ($arguments as $key => $argument) {
            $key = strval($key);
            if ($argument->isRequired() && empty($commandParser->getArgumentValue($key))) {
                throw new InvalidArgumentException(sprintf('Argument "%s" is required for command "%s".', $argument->getName(), $command->getName()));
            }
            if ($commandParser->hasArgument($key)) {
                $argv["--{$argument->getName()}"] = $commandParser->getArgumentValue($key);
            }else {
                $argv["--{$argument->getName()}"] = $argument->getDefaultValue();
            }
        }

        if (count($commandParser->getArguments()) > count($arguments)) {
            throw new InvalidArgumentException(sprintf('Too many arguments for command "%s". Expected %d, got %d.', $command->getName(), count($arguments), count($commandParser->getArguments())));
        }

        $command->execute(new Input($commandParser->getCommandName(), $argvOptions, $argv), $output);
    }

    private function showCommandHelp(CommandInterface $selectedCommand, OutputInterface $output): void
    {
        $consoleOutput = new ConsoleOutput($output);
        $consoleOutput->writeColor('Description:', 'yellow');
        $consoleOutput->write(PHP_EOL);
        $consoleOutput->writeln($selectedCommand->getDescription());
        $consoleOutput->write(PHP_EOL);

        $consoleOutput->writeColor('Arguments:', 'yellow');
        $consoleOutput->write(PHP_EOL);
        $arguments = [];
        foreach ($selectedCommand->getArguments() as $argument) {
            $arguments[$argument->getName()] = $argument->getDescription();
        }
        $consoleOutput->listKeyValues($arguments, true);

        $consoleOutput->writeColor('Options:', 'yellow');
        $consoleOutput->write(PHP_EOL);
        $options = [];
        foreach ($selectedCommand->getOptions() as $option) {
            $name = sprintf('--%s', $option->getName());
            if ($option->getShortcut() !== null) {
                $name = sprintf('-%s, --%s', $option->getShortcut(), $option->getName());
            }

            if (!$option->isFlag()) {
                $name = sprintf('%s=VALUE', $name);
            }
            $options[$name] = $option->getDescription();
        }
        $consoleOutput->listKeyValues($options, true);
    }

    private static function stringStartsWith(string $haystack, string $needle): bool
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}
