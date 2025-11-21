<?php

namespace PhpDevCommunity\Console\Option;

final class CommandOption
{
    private string $name;
    private ?string $shortcut;
    private ?string $description;
    private bool $isFlag;

    public function __construct(
        string  $name,
        ?string $shortcut = null,
        ?string $description = null,
        bool    $isFlag = false
    )
    {

        if ($name === '') {
            throw new \InvalidArgumentException("Option name cannot be empty.");
        }

        foreach (explode('-', $name) as $part) {
            if ($part === '' || !ctype_alpha($part)) {
                throw new \InvalidArgumentException("Option name must contain only letters and dashes. '$name' is invalid.");
            }
        }

        if ($shortcut !== null && (strlen($shortcut) !== 1 || !ctype_alpha($shortcut))) {
            throw new \InvalidArgumentException('Shortcut must be a single character and contain only letters. "' . $shortcut . '" is invalid.');
        }

        $this->name = strtolower($name);
        $this->shortcut = $shortcut !== null ? strtolower($shortcut) : null;
        $this->description = $description;
        $this->isFlag = $isFlag;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortcut(): ?string
    {
        return $this->shortcut;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isFlag(): bool
    {
        return $this->isFlag;
    }

    public static function flag(string $name, ?string $shortcut = null, ?string $description = null): self {
        return new self($name, $shortcut, $description, true);
    }
    
    public static function withValue(string $name, ?string $shortcut = null, ?string $description = null): self {
        return new self($name, $shortcut, $description, false);
    }
}