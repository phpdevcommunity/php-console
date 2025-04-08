<?php

namespace PhpDevCommunity\Console;


final class Input implements InputInterface
{
    /**
     * @var string|null
     */
    private ?string $cmdName;
    private array $options = [];
    private array $arguments = [];

    public function __construct(string $cmdName, array $options = [], array $arguments = [])
    {
        $this->cmdName = $cmdName;
        $this->options = $options;
        $this->arguments = $arguments;
    }

    public function getCommandName(): ?string
    {
        return $this->cmdName;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function hasOption(string $name): bool
    {
        if (!self::startsWith($name, '--')) {
            $name = "--$name";
        }
        return array_key_exists($name, $this->options);
    }

    public function getOptionValue(string $name)
    {
        if (!self::startsWith($name, '--')) {
            $name = "--$name";
        }

        if (!$this->hasOption($name)) {
            throw new \InvalidArgumentException(sprintf('Option "%s" is not defined.', $name));
        }
        return $this->options[$name];
    }

    public function getArgumentValue(string $name)
    {
        if (!self::startsWith($name, '--')) {
            $name = "--$name";
        }
        if (!$this->hasArgument($name)) {
            throw new \InvalidArgumentException(sprintf('Argument "%s" is not defined.', $name));
        }
        return $this->arguments[$name];
    }

    public function hasArgument(string $name): bool
    {
        if (!self::startsWith($name, '--')) {
            $name = "--$name";
        }
        return array_key_exists($name, $this->arguments);
    }

    private static function startsWith(string $haystack, string $needle): bool
    {
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
