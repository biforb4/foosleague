<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League\Event;

use FoosCommon\Model\DomainEvent;
use FoosLeague\Domain\League\PendingGame;

final class LeagueGameWasEnded extends DomainEvent
{
    private PendingGame $pendingGame;

    public function __construct(PendingGame $pendingGame)
    {
        $this->pendingGame = $pendingGame;
    }

    public function getPendingGame(): PendingGame
    {
        return $this->pendingGame;
    }

    public function eventVersion(): int
    {
        return 1;
    }
}
