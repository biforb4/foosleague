<?php

declare(strict_types=1);

namespace FoosTeam\Model\Team;

class Name
{
    private string $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromString(string $name): Name
    {
        return new self($name);
    }

    public function asString(): string
    {
        return $this->name;
    }
}
