<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League;

final class Schedule
{
    /** @var array<PendingGame> */
    private array $pendingGames = [];
    /** @var array<FinishedGame> */
    private array $endedGames = [];

    public function addGame(PendingGame $pendingGame): void
    {
        $this->pendingGames[] = $pendingGame;
    }

    public function endGame(PendingGame $pendingGame): void
    {
        foreach ($this->pendingGames as $i => $game) {
            if ($pendingGame->equals($game)) {
                $this->endedGames[] = $pendingGame->end();
                unset($this->pendingGames[$i]);
                reset($this->pendingGames);
                return;
            }
        }
        throw LeagueException::pendingGameNotFound();
    }
}
