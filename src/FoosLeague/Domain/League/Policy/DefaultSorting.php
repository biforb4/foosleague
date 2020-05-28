<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League\Policy;

use FoosLeague\Domain\League\Record;

final class DefaultSorting implements SortingRankingsStrategyInterface
{
    public function __invoke(Record $teamOne, Record $teamTwo): int
    {
        $result = $teamTwo->getPoints() <=> $teamOne->getPoints();
        if ($result === 0) {
            $result = $teamTwo->getSetsPlusMinus() <=> $teamOne->getSetsPlusMinus();
            if ($result === 0) {
                $result = $teamTwo->getGoalsPlusMinus() <=> $teamOne->getGoalsPlusMinus();
            }
        }
        return $result;
    }
}
