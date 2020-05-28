<?php

declare(strict_types=1);

namespace FoosLeague\Domain\Game;

use Webmozart\Assert\Assert;

final class Set
{
    private int $homeScore;
    private int $awayScore;

    private function __construct(int $homeScore, int $awayScore)
    {
        Assert::allGreaterThanEq([$homeScore, $awayScore], 0);
        Assert::allLessThanEq([$homeScore, $awayScore], 10);
        if ($homeScore === 10) {
            Assert::lessThan($awayScore, 10);
        } else {
            Assert::lessThan($homeScore, 10);
        }

        $this->homeScore = $homeScore;
        $this->awayScore = $awayScore;
    }

    public static function withPoints(int $homeScore, int $awayScore): Set
    {
        return new self($homeScore, $awayScore);
    }

    public function getHomeScore(): int
    {
        return $this->homeScore;
    }

    public function getAwayScore(): int
    {
        return $this->awayScore;
    }
}
