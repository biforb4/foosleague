<?php

declare(strict_types=1);

namespace FoosLeague\Domain\Game;

interface GameRepositoryInterface
{
    public function add(Game $game): void;

    public function remove(Game $game): void;

    public function findById(GameId $gameId): ?Game;

    /** @return array<Game|null> */
    public function all(): array;
}
