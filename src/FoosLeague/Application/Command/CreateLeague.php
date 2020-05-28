<?php

declare(strict_types=1);

namespace FoosLeague\Application\Command;

use FoosLeague\Domain\League\LeagueId;
use FoosLeague\Domain\League\Name;
use FoosCommon\Model\Owner\OwnerId;

final class CreateLeague
{
    private OwnerId $owner;
    private Name $name;
    private LeagueId $id;

    public function __construct(OwnerId $owner, LeagueId $id, Name $name)
    {
        $this->owner = $owner;
        $this->name = $name;
        $this->id = $id;
    }

    public function getOwner(): OwnerId
    {
        return $this->owner;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getId(): LeagueId
    {
        return $this->id;
    }
}
