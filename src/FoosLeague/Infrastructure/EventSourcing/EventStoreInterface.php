<?php

declare(strict_types=1);

namespace FoosLeague\Infrastructure\EventSourcing;

use FoosCommon\Model\DomainEvent;

interface EventStoreInterface
{
    /**
     * @param DomainEvent[] $events
     * @psalm-param array<int,DomainEvent> $events
     */
    public function appendWith(EventStreamId $eventStreamId, array $events): void;

    public function eventStreamFor(EventStreamId $eventStreamId): EventStream;
}
