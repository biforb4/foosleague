<?php

declare(strict_types=1);

namespace FoosLeague\Domain\Game;

use FoosCommon\Model\AggregateRoot;
use FoosLeague\Domain\League\LeagueId;
use FoosCommon\Model\Team\TeamId;

final class Game extends AggregateRoot
{
    private const SCORE_PENDING = '-:-';
    private GameId $id;
    private TeamId $homeTeam;
    private TeamId $awayTeam;
    private LeagueId $leagueId;
    private ?Score $score = null;

    public function __construct(GameId $id, LeagueId $leagueId, TeamId $homeTeam, TeamId $awayTeam)
    {
        $this->id = $id;
        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;
        $this->leagueId = $leagueId;
    }

    public function getId(): GameId
    {
        return $this->id;
    }

    public function end(Score $score): void
    {
        $this->score = $score;

        $this->raiseEvent(
            new GameEndedEvent(
                $this->id,
                $this->leagueId,
                $this->getWinner(),
                $this->getLoser(),
                $this->getSetDifference(),
                $this->getPointsDifference()
            )
        );
    }

    public function getScoreAsString(): string
    {
        return $this->score ? $this->score->asString() : self::SCORE_PENDING;
    }

    private function getWinner(): TeamId
    {
        $this->assertGameEnded();
        if ($this->score->getHomeTeamWonSets() > $this->score->getAwayTeamWonSets()) {
            return $this->homeTeam;
        }

        return $this->awayTeam;
    }

    private function getLoser(): TeamId
    {
        $this->assertGameEnded();
        if ($this->score->getHomeTeamWonSets() < $this->score->getAwayTeamWonSets()) {
            return $this->homeTeam;
        }

        return $this->awayTeam;
    }

    private function getSetDifference(): int
    {
        $this->assertGameEnded();
        return abs($this->score->getHomeTeamWonSets() - $this->score->getAwayTeamWonSets());
    }

    private function getPointsDifference(): int
    {
        $this->assertGameEnded();
        return abs($this->score->getHomeScoredPointsTotal() - $this->score->getAwayScoredPointsTotal());
    }

    public function getHomeTeamId(): TeamId
    {
        return $this->homeTeam;
    }

    public function getAwayTeamId(): TeamId
    {
        return $this->awayTeam;
    }

    private function assertGameEnded(): void
    {
        if ($this->score === null) {
            throw GameException::gameNotEnded();
        }
    }
}
