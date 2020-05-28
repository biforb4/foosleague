<?php

declare(strict_types=1);

namespace FoosLeague\Domain\Game;

use Webmozart\Assert\Assert;

final class Score
{
    /** @var array<int, Set> */
    private array $sets;

    /** @param array<int, Set> $sets */
    private function __construct(array $sets)
    {
        Assert::countBetween($sets, 2, 3);

        $this->sets = $sets;
    }

    /**
     * @param array<int, Set> $sets
     */
    public static function withSets(array $sets): Score
    {
        return new self($sets);
    }

    public function asString(): string
    {
        $sets = '';
        /** @var Set $set */
        foreach ($this->sets as $set) {
            $sets .= sprintf('%s:%s ', $set->getHomeScore(), $set->getAwayScore());
        }
        return sprintf(
            '%s:%s (%s)',
            $this->getHomeTeamWonSets(),
            $this->getAwayTeamWonSets(),
            rtrim($sets)
        );
    }

    public function getHomeTeamWonSets(): int
    {
        $won = 0;
        /** @var Set $set */
        foreach ($this->sets as $set) {
            if ($set->getHomeScore() === 10) {
                $won++;
            }
        }

        return $won;
    }

    public function getAwayTeamWonSets(): int
    {
        $won = 0;
        /** @var Set $set */
        foreach ($this->sets as $set) {
            if ($set->getAwayScore() === 10) {
                $won++;
            }
        }

        return $won;
    }

    public function getHomeScoredPointsTotal(): int
    {
        $points = 0;
        /** @var Set $set */
        foreach ($this->sets as $set) {
            $points += $set->getHomeScore();
        }

        return $points;
    }

    public function getAwayScoredPointsTotal(): int
    {
        $points = 0;
        /** @var Set $set */
        foreach ($this->sets as $set) {
            $points += $set->getAwayScore();
        }

        return $points;
    }
}
