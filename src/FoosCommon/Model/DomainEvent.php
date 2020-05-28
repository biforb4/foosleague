<?php

declare(strict_types=1);

namespace FoosCommon\Model;

abstract class DomainEvent
{
    abstract public function eventVersion(): int;

    public function occurredOn(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
