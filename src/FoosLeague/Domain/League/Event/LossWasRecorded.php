<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League\Event;

use FoosCommon\Model\DomainEvent;
use FoosCommon\Model\Team\TeamId;

final class LossWasRecorded extends DomainEvent
{
    private TeamId $teamId;
    private int $sets;
    private int $goals;

    public function __construct(TeamId $teamId, int $sets, int $goals)
    {
        $this->teamId = $teamId;
        $this->sets = $sets;
        $this->goals = $goals;
    }

    public function getTeamId(): TeamId
    {
        return $this->teamId;
    }

    public function getSets(): int
    {
        return $this->sets;
    }

    public function getGoals(): int
    {
        return $this->goals;
    }

    public function eventVersion(): int
    {
        return 1;
    }
}
