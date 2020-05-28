<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League\Event;

use FoosCommon\Model\DomainEvent;
use FoosLeague\Domain\League\LeagueId;
use FoosLeague\Domain\League\Name;
use FoosCommon\Model\Owner\OwnerId;

final class LeagueWasCreated extends DomainEvent
{
    private OwnerId $owner;
    private LeagueId $leagueId;
    private Name $name;

    public function __construct(OwnerId $owner, LeagueId $leagueId, Name $name)
    {
        $this->owner = $owner;
        $this->leagueId = $leagueId;
        $this->name = $name;
    }

    public function eventVersion(): int
    {
        return 1;
    }

    public function getOwner(): OwnerId
    {
        return $this->owner;
    }

    public function getLeagueId(): LeagueId
    {
        return $this->leagueId;
    }

    public function getName(): Name
    {
        return $this->name;
    }
}
