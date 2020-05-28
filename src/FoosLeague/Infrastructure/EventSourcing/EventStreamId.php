<?php

declare(strict_types=1);

namespace FoosLeague\Infrastructure\EventSourcing;

use FoosCommon\Model\AggregateIdInterface;
use FoosCommon\Model\Owner\OwnerId;

class EventStreamId
{
    private string $streamName;
    private int $streamVersion;

    public function __construct(string $streamName, int $streamVersion)
    {
        $this->streamName = $streamName;
        $this->streamVersion = $streamVersion;
    }

    public static function fromOwnerAndAggregateId(OwnerId $owner, AggregateIdInterface $id): EventStreamId
    {
        return new self($owner->asString() . ':' . $id->asString(), 1);
    }

    public function getStreamName(): string
    {
        return $this->streamName;
    }

    public function getStreamVersion(): int
    {
        return $this->streamVersion;
    }
}
