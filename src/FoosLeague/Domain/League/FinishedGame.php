<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League;

use FoosCommon\Model\Team\TeamId;

final class FinishedGame
{
    private TeamId $homeTeam;
    private TeamId $awayTeam;

    private function __construct(TeamId $homeTeam, TeamId $awayTeam)
    {
        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;
    }

    public static function fromPendingGame(PendingGame $pendingGame): FinishedGame
    {
        return new self($pendingGame->getHomeTeamId(), $pendingGame->getAwayTeamId());
    }

    public function getHomeTeam(): TeamId
    {
        return $this->homeTeam;
    }

    public function getAwayTeam(): TeamId
    {
        return $this->awayTeam;
    }
}
