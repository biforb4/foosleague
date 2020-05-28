<?php

declare(strict_types=1);

namespace FoosCommon\Model;

use FoosLeague\Infrastructure\EventSourcing\EventStream;

abstract class EventSourcedAggregateRoot
{
    /** @var array<int, DomainEvent> */
    private array $domainEvents = [];
    private int $streamVersion;

    /** @param array<int, DomainEvent> $domainEvents */
    protected function __construct(array $domainEvents, int $streamVersion)
    {
        foreach ($domainEvents as $event) {
            $this->when($event);
        }

        $this->streamVersion = $streamVersion;
    }

    abstract public static function fromEventStream(EventStream $eventStream): EventSourcedAggregateRoot;

    abstract protected function when(DomainEvent $event): void;

    protected function apply(DomainEvent $event): void
    {
        $this->when($event);
        $this->domainEvents[] = $event;
    }

    public function getNewStreamVersion(): int
    {
        return $this->streamVersion + 1;
    }

    /**
     * @return array<int, DomainEvent>
     */
    public function getPendingEvents(): array
    {
        return $this->domainEvents;
    }
}
