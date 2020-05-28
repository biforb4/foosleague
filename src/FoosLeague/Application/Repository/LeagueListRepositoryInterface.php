<?php

declare(strict_types=1);

namespace FoosLeague\Application\Repository;

use FoosLeague\Application\Dto\League;
use FoosCommon\Model\Owner\OwnerId;

interface LeagueListRepositoryInterface
{
    /** @return array<int, League> */
    public function findAllLeaguesFor(OwnerId $owner): array;
    public function save(League $league): bool;
}
