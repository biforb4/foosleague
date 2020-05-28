<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League;

use FoosCommon\Model\Team\TeamId;

final class Record
{
    private const WIN_POINTS = 2;
    private TeamId $teamId;
    private int $points;
    private int $setsPlusMinus;
    private int $goalsPlusMinus;

    private function __construct(TeamId $teamId, int $points, int $setsPlusMinus, int $goalsPlusMinus)
    {
        $this->teamId = $teamId;
        $this->points = $points;
        $this->setsPlusMinus = $setsPlusMinus;
        $this->goalsPlusMinus = $goalsPlusMinus;
    }

    public static function firstWin(TeamId $teamId, int $setsPlusMinus, int $goalsPlusMinus): Record
    {
        return new self($teamId, self::WIN_POINTS, $setsPlusMinus, $goalsPlusMinus);
    }

    public static function firstLoss(TeamId $teamId, int $setsPlusMinus, int $goalsPlusMinus): Record
    {
        return new self($teamId, 0, $setsPlusMinus, $goalsPlusMinus);
    }

    public function win(int $setsPlusMinus, int $goalsPlusMinus): Record
    {
        return new self(
            $this->teamId,
            $this->points + self::WIN_POINTS,
            $this->setsPlusMinus + $setsPlusMinus,
            $this->goalsPlusMinus + $goalsPlusMinus
        );
    }

    public function loss(int $setsPlusMinus, int $goalsPlusMinus): Record
    {
        return new self(
            $this->teamId,
            $this->points,
            $this->setsPlusMinus - $setsPlusMinus,
            $this->goalsPlusMinus - $goalsPlusMinus
        );
    }

    public function getTeamId(): TeamId
    {
        return $this->teamId;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function getSetsPlusMinus(): int
    {
        return $this->setsPlusMinus;
    }

    public function getGoalsPlusMinus(): int
    {
        return $this->goalsPlusMinus;
    }
}
