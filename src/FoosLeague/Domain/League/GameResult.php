<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League;

use FoosCommon\Model\Team\TeamId;
use Webmozart\Assert\Assert;

final class GameResult
{
    private TeamId $winner;
    private TeamId $loser;
    private int $setDifference;
    private int $goalDifference;

    public function __construct(
        TeamId $winner,
        TeamId $loser,
        int $setDifference,
        int $goalDifference
    ) {
        Assert::range($setDifference, 1, 2);
        Assert::range($goalDifference, 1, 20);
        $this->winner = $winner;
        $this->loser = $loser;
        $this->setDifference = $setDifference;
        $this->goalDifference = $goalDifference;
    }

    public function getWinner(): TeamId
    {
        return $this->winner;
    }

    public function getLoser(): TeamId
    {
        return $this->loser;
    }

    public function getSetDifference(): int
    {
        return $this->setDifference;
    }

    public function getGoalDifference(): int
    {
        return $this->goalDifference;
    }
}
