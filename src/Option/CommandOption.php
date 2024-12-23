<?php

namespace PhpDevCommunity\Console\Option;

final class CommandOption
{
    private string $name;
    private ?string $shortcut;
    private ?string $description;
    private bool $isFlag;

    public function __construct(
        string $name,
        ?string $shortcut = null,
        ?string $description = null,
        bool $isFlag = false
    ) {
        $this->name = $name;
        $this->shortcut = $shortcut;
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
}