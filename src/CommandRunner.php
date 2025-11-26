<?php

namespace PhpDevCommunity\Console;

use InvalidArgumentException;
use PhpDevCommunity\Console\Command\CommandInterface;
use PhpDevCommunity\Console\Command\HelpCommand;
use PhpDevCommunity\Console\Option\CommandOption;
use PhpDevCommunity\Console\Output\ConsoleOutput;
use Throwable;
use const PHP_EOL;

final class CommandRunner
{
    const CLI_ERROR = 1;
    const CLI_SUCCESS = 0;
    public const CLI_COMMAND_NOT_FOUND = 10;    // Aucune commande trouvée
    public const CLI_INVALID_ARGUMENTS = 11;    // Arguments invalides
    public const CLI_AMBIGUOUS_COMMAND = 12;    // Plusieurs correspondances possibles

    /**
     * @var CommandInterface[]
     */
    private array $commands = [];

    private HelpCommand $defaultCommand;

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
        if ($commandParser->hasOption('verbose') || $commandParser->hasOption('v')) {
            $output->setVerbose(true);
        }

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
                throw new InvalidArgumentException(sprintf('Command "%s" is not defined.', $commandParser->getCommandName()), self::CLI_COMMAND_NOT_FOUND);
            }

            if (count($commands) > 1) {
                $names = [];
                foreach ($commands as $command) {
                    $names[$command->getName()] = $command->getDescription();
                }
                $consoleOutput = new ConsoleOutput($output);
                $consoleOutput->error(sprintf('Command "%s" is ambiguous.', $commandParser->getCommandName()));
                $consoleOutput->listKeyValues($names, true);
                return self::CLI_AMBIGUOUS_COMMAND;
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
            return in_array($e->getCode(), [self::CLI_COMMAND_NOT_FOUND, self::CLI_INVALID_ARGUMENTS]) ? $e->getCode() : self::CLI_ERROR;
        }

    }

    private function execute(CommandInterface $command, CommandParser $commandParser, OutputInterface $output)
    {
        $argvOptions = [];
        $options = $command->getOptions();
        $forbidden = ['help', 'h', 'verbose', 'v'];
        foreach ($options as $option) {
            $name     = $option->getName();
            $shortcut = $option->getShortcut();
            if (in_array($name, $forbidden, true) || ($shortcut !== null && in_array($shortcut, $forbidden, true))) {
                $invalid = in_array($name, $forbidden, true) ? $name : $shortcut;
                throw new \InvalidArgumentException(
                    sprintf(
                        'The option "%s" is reserved and cannot be used with the "%s" command.',
                        $invalid,
                        $command->getName()
                    )
                );
            }
        }

        $options[] = CommandOption::flag('verbose', 'v', 'Enable verbose output');
        foreach ($options as $option) {
            if ($option->isFlag()) {
                $argvOptions["--{$option->getName()}"] = false;
            }elseif ($option->getDefaultValue() !== null) {
                $argvOptions["--{$option->getName()}"] = $option->getDefaultValue();
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
            $keyArg = $argument->getName();
            if ($argument->isRequired() && (!$commandParser->hasArgument($key) || empty($commandParser->getArgumentValue($key)))) {
                throw new InvalidArgumentException(sprintf('Argument "%s" is required for command "%s".', $argument->getName(), $command->getName()));
            }
            if ($commandParser->hasArgument($key)) {
                $argv["--{$keyArg}"] = $commandParser->getArgumentValue($key);
            }else {
                $argv["--{$keyArg}"] = $argument->getDefaultValue();
            }
        }

        if (count($commandParser->getArguments()) > count($arguments)) {
            throw new InvalidArgumentException(sprintf('Too many arguments for command "%s". Expected %d, got %d.', $command->getName(), count($arguments), count($commandParser->getArguments())));
        }

        $startTime = microtime(true);
        $input = new Input($commandParser->getCommandName(), $argvOptions, $argv);
        $command->execute($input, $output);
        $endTime    = microtime(true);
        $peakMemoryBytes            = memory_get_peak_usage(true);
        $peakMemoryMB               = round($peakMemoryBytes / 1024 / 1024, 2);
        $duration                   = round($endTime - $startTime, 2);
        if ($output->isVerbose()) {
            $output->writeln(sprintf(
                'Execution time: %.2fs; Peak memory usage: %.2f MB',
                $duration,
                $peakMemoryMB
            ));
        }
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
            if ($option->getDefaultValue() !== null) {
                $name = sprintf('%s (default: %s)', $name, $option->getDefaultValue());
            }

            $options[$name] = $option->getDescription();
        }
        $consoleOutput->listKeyValues($options, true);
    }

    private static function stringStartsWith(string $command, string $input): bool
    {
        $commandParts = explode(':', $command);
        $inputParts = explode(':', $input);
        foreach ($inputParts as $i => $inPart) {
            $cmdPart = $commandParts[$i] ?? null;

            if ($cmdPart === null || strpos($cmdPart, $inPart) !== 0) {
                return false;
            }
        }

        return true;
    }
}
