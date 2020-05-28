<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League;

use FoosCommon\Model\AggregateIdInterface;

final class LeagueId implements AggregateIdInterface
{
    private string $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function asString(): string
    {
        return $this->name;
    }
}
