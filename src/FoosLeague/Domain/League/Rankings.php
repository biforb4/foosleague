<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League;

use FoosLeague\Domain\League\Policy\SortingRankingsStrategyInterface;
use FoosCommon\Model\Team\TeamId;
use Webmozart\Assert\Assert;

final class Rankings
{
    /** @var array<Record> */
    private array $records = [];

    public function recordWin(TeamId $teamId, int $sets, int $goals): void
    {
        /** @var Record $record */
        foreach ($this->records as $i => $record) {
            if ($record->getTeamId()->equals($teamId)) {
                $this->records[$i] = $record->win($sets, $goals);
                return;
            }
        }
        $this->records[] = Record::firstWin($teamId, $sets, $goals);
    }

    public function recordLoss(TeamId $teamId, int $sets, int $goals): void
    {
        /** @var Record $record */
        foreach ($this->records as $i => $record) {
            if ($record->getTeamId()->equals($teamId)) {
                $this->records[$i] = $record->loss($sets, $goals);
                return;
            }
        }
        $this->records[] = Record::firstLoss($teamId, $sets, $goals);
    }

    /** @return array<array-key, TeamId> */
    public function best(int $howMany, SortingRankingsStrategyInterface $sortingRankingsStrategy): array
    {
        Assert::oneOf($howMany, [2, 4, 8]);

        $this->sortRecords($sortingRankingsStrategy);
        $best = array_slice($this->records, 0, $howMany);

        $teams = [];
        foreach ($best as $record) {
            $teams[] = $record->getTeamId();
        }

        return $teams;
    }

    private function sortRecords(SortingRankingsStrategyInterface $sortingRankingsStrategy): void
    {
        usort($this->records, $sortingRankingsStrategy);
    }
}
