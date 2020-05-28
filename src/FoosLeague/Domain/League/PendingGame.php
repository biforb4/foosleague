<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League;

use FoosCommon\Model\Team\TeamId;

final class PendingGame
{
    private TeamId $homeTeam;
    private TeamId $awayTeam;

    public function __construct(TeamId $homeTeam, TeamId $awayTeam)
    {
        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;
    }


    public function getHomeTeamId(): TeamId
    {
        return $this->homeTeam;
    }

    public function getAwayTeamId(): TeamId
    {
        return $this->awayTeam;
    }

    public function end(): FinishedGame
    {
        return FinishedGame::fromPendingGame($this);
    }

    public function equals(PendingGame $game): bool
    {
        return $this->getAwayTeamId()->equals($game->getAwayTeamId())
            && $this->getHomeTeamId()->equals($game->getHomeTeamId());
    }
}
