<?php

declare(strict_types=1);

namespace FoosTeam\Model\Player;

use FoosCommon\Model\DomainEvent;

final class PlayerCreatedEvent extends DomainEvent
{
    private string $playerId;

    public function __construct(string $playerId)
    {
        $this->playerId = $playerId;
    }

    public function getPlayerId(): string
    {
        return $this->playerId;
    }

    public function eventVersion(): int
    {
        return 1;
    }
}
