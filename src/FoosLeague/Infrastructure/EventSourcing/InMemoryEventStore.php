<?php

declare(strict_types=1);

namespace FoosLeague\Infrastructure\EventSourcing;

use FoosCommon\Model\DomainEvent;

class InMemoryEventStore implements EventStoreInterface
{
    /** @var array<array<int, DomainEvent>>  */
    private array $eventStore = [];

    public function appendWith(EventStreamId $eventStreamId, array $events): void
    {
        $index = 0;
        foreach ($events as $event) {
            $this->eventStore[$eventStreamId->getStreamName()][$eventStreamId->getStreamVersion() + $index++] = $event;
        }
    }

    public function eventStreamFor(EventStreamId $eventStreamId): EventStream
    {
        $events = [];
        $version = 0;
        foreach ($this->eventStore[$eventStreamId->getStreamName()] as $version => $event) {
            $events[] = $event;
        }

        return new EventStream($events, $version);
    }
}
