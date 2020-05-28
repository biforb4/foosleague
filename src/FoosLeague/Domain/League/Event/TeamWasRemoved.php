<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League\Event;

use FoosCommon\Model\DomainEvent;
use FoosLeague\Domain\League\LeagueId;
use FoosCommon\Model\Team\TeamId;

final class TeamWasRemoved extends DomainEvent
{
    private LeagueId $leagueId;
    private TeamId $teamId;

    public function __construct(LeagueId $leagueId, TeamId $teamId)
    {
        $this->leagueId = $leagueId;
        $this->teamId = $teamId;
    }

    public function eventVersion(): int
    {
        return 1;
    }

    public function getLeagueId(): LeagueId
    {
        return $this->leagueId;
    }

    public function getTeamId(): TeamId
    {
        return $this->teamId;
    }
}
