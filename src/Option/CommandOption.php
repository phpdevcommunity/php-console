<?php

namespace PhpDevCommunity\Console\Option;

final class CommandOption
{
    private string $name;
    private ?string $shortcut;
    private ?string $description;
    private bool $isFlag;

    /**
     * @var bool|float|int|string
     */
    private $default = null;

    public function __construct(
        string  $name,
        ?string $shortcut = null,
        ?string $description = null,
        bool    $isFlag = false,
        $default = null
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

        if (!is_null($default) && !is_scalar($default)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid default value: expected a scalar (int, float, string, or bool), got "%s".',
                    gettype($default)
                )
            );
        }

        $this->name = strtolower($name);
        $this->shortcut = $shortcut !== null ? strtolower($shortcut) : null;
        $this->description = $description;
        $this->isFlag = $isFlag;
        $this->default = $default;
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

    public function getDefaultValue()
    {
        return $this->default;
    }

    public static function flag(string $name, ?string $shortcut = null, ?string $description = null): self {
        return new self($name, $shortcut, $description, true, false);
    }
    
    public static function withValue(string $name, ?string $shortcut = null, ?string $description = null, $default = null): self {
        return new self($name, $shortcut, $description, false, $default);
    }

}
