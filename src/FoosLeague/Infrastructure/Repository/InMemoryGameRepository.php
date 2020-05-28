<?php

declare(strict_types=1);

namespace FoosLeague\Infrastructure\Repository;

use FoosLeague\Domain\Game\Game;
use FoosLeague\Domain\Game\GameId;
use FoosLeague\Domain\Game\GameRepositoryInterface;

final class InMemoryGameRepository implements GameRepositoryInterface
{
    /** @var array<Game|null> */
    private array $games = [];
    public function add(Game $game): void
    {
        $this->games[$game->getId()->asString()] = $game;
    }

    public function remove(Game $game): void
    {
        unset($this->games[$game->getId()->asString()]);
    }

    public function findById(GameId $gameId): ?Game
    {
        return $this->games[$gameId->asString()] ?: null;
    }

    /**
     * @return (Game|null)[]
     * @psalm-return array<array-key, Game|null>
     */
    public function all(): array
    {
        return $this->games;
    }
}
