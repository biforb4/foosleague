<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League;

use FoosCommon\Model\Owner\OwnerId;

interface LeagueRepositoryInterface
{
    public function save(League $league): void;
    public function leagueOfId(OwnerId $owner, LeagueId $leagueId): League;
}
