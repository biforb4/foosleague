<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League\Event;

use FoosCommon\Model\DomainEvent;
use FoosLeague\Domain\Game\GameId;
use FoosLeague\Domain\League\LeagueId;
use FoosCommon\Model\Team\TeamId;

final class GameWasAdded extends DomainEvent
{
    private LeagueId $leagueId;
    private TeamId $homeTeamId;
    private TeamId $awayTeamId;

    public function __construct(LeagueId $leagueId, TeamId $homeTeamId, TeamId $awayTeamId)
    {
        $this->leagueId = $leagueId;
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
    }

    public function getHomeTeamId(): TeamId
    {
        return $this->homeTeamId;
    }

    public function getAwayTeamId(): TeamId
    {
        return $this->awayTeamId;
    }

    public function getLeagueId(): LeagueId
    {
        return $this->leagueId;
    }

    public function eventVersion(): int
    {
        return 1;
    }
}
