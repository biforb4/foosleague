<?php

declare(strict_types=1);

namespace FoosLeague\Application\Dto;

use FoosLeague\Domain\League\Event\LeagueWasCreated;

final class League
{
    private string $name;
    private string $ownerId;

    private function __construct(string $name, string $ownerId)
    {
        $this->name = $name;
        $this->ownerId = $ownerId;
    }

    public static function fromLeagueWasCreatedEvent(LeagueWasCreated $event): League
    {
        return new self($event->getName()->asString(), $event->getOwner()->asString());
    }

    public function fromPdo(string $leagueName, string $ownerId): League
    {
        return new self($leagueName, $ownerId);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }
}
