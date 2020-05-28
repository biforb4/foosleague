<?php

declare(strict_types=1);

namespace FoosLeague\Infrastructure\Repository;

use FoosLeague\Domain\League\League;
use FoosLeague\Domain\League\LeagueId;
use FoosLeague\Domain\League\LeagueRepositoryInterface;
use FoosCommon\Model\Owner\OwnerId;
use FoosLeague\Infrastructure\EventSourcing\EventStoreInterface;
use FoosLeague\Infrastructure\EventSourcing\EventStreamId;

final class EventStoreLeagueRepository implements LeagueRepositoryInterface
{
    private EventStoreInterface $eventStore;

    public function __construct(EventStoreInterface $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function save(League $league): void
    {
        $this->eventStore->appendWith(
            EventStreamId::fromOwnerAndAggregateId($league->getOwner(), $league->getLeagueId()),
            $league->getPendingEvents()
        );
    }

    public function leagueOfId(OwnerId $owner, LeagueId $leagueId): League
    {
        $eventStream =  $this->eventStore->eventStreamFor(EventStreamId::fromOwnerAndAggregateId($owner, $leagueId));
        return League::fromEventStream($eventStream);
    }
}
