<?php

declare(strict_types=1);

namespace FoosCommon\Model;

abstract class AggregateRoot
{
    /** @var array<DomainEvent> */
    private array $events = [];

    protected function raiseEvent(DomainEvent $event): void
    {
        $this->events[] = $event;
    }

    public function getEvents(): array
    {
        return $this->events;
    }
}
