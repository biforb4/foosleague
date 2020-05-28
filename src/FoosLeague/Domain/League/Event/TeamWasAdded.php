<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League\Event;

use FoosCommon\Model\DomainEvent;
use FoosLeague\Domain\League\LeagueId;
use FoosCommon\Model\Player\PlayerId;
use FoosCommon\Model\Team\TeamId;

final class TeamWasAdded extends DomainEvent
{
    private TeamId $teamId;
    private LeagueId $leagueId;
    /** @var array<int, PlayerId> */
    private array $playerIds;

    /** @param array<int, PlayerId> $playerIds */
    public function __construct(
        LeagueId $leagueId,
        TeamId $teamId,
        array $playerIds
    ) {
        $this->teamId = $teamId;
        $this->leagueId = $leagueId;
        $this->playerIds = $playerIds;
    }

    public function eventVersion(): int
    {
        return 1;
    }

    public function getTeamId(): TeamId
    {
        return $this->teamId;
    }

    public function getLeagueId(): LeagueId
    {
        return $this->leagueId;
    }

    /** @return array<int, PlayerId> */
    public function getPlayerIds(): array
    {
        return $this->playerIds;
    }
}
