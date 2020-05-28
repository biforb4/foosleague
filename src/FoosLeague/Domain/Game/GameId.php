<?php

declare(strict_types=1);

namespace FoosLeague\Domain\Game;

use FoosCommon\Model\AggregateIdInterface;
use Webmozart\Assert\Assert;

final class GameId implements AggregateIdInterface
{
    private string $id;

    private function __construct(string $id)
    {
        Assert::uuid($id);
        $this->id = $id;
    }

    public static function fromString(string $id): GameId
    {
        return new self($id);
    }

    public function asString(): string
    {
        return $this->id;
    }
}
