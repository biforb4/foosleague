<?php

declare(strict_types=1);

namespace FoosLeague\Infrastructure\EventSourcing;

use FoosCommon\Model\DomainEvent;

class EventStream
{
    /**
     * @var DomainEvent[]
     * @psalm-var array<int, DomainEvent>
     */
    private array $events;
    private int $version;

    /** @param array<int, DomainEvent> $events */
    public function __construct(array $events, int $version)
    {
        $this->events = $events;
        $this->version = $version;
    }

    /** @return array<int, DomainEvent> */
    public function getEvents(): array
    {
        return $this->events;
    }

    public function getVersion(): int
    {
        return $this->version;
    }
}
