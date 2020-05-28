<?php

declare(strict_types=1);

namespace FoosLeague\Application\Query;

use FoosCommon\Model\Owner\OwnerId;

final class ShowLeagues implements Query
{
    private OwnerId $owner;

    public function __construct(OwnerId $owner)
    {
        $this->owner = $owner;
    }

    public function getOwner(): OwnerId
    {
        return $this->owner;
    }
}
