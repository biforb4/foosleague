<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League\Policy;

use FoosLeague\Domain\League\Record;

interface SortingRankingsStrategyInterface
{
    public function __invoke(Record $teamOne, Record $teamTwo): int;
}
