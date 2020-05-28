<?php

declare(strict_types=1);

namespace FoosTeam\Model\Team;

use FoosCommon\Model\DomainEvent;

final class TeamCreatedEvent extends DomainEvent
{
    private string $teamId;

    public function __construct(string $teamId)
    {
        $this->teamId = $teamId;
    }

    public function getTeamId(): string
    {
        return $this->teamId;
    }

    public function eventVersion(): int
    {
        return 1;
    }
}
