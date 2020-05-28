<?php

declare(strict_types=1);

namespace FoosLeague\Domain\Game;

use FoosCommon\Model\DomainEvent;
use FoosLeague\Domain\League\LeagueId;
use FoosCommon\Model\Team\TeamId;

final class GameEndedEvent extends DomainEvent
{
    private GameId $gameId;
    private LeagueId $leagueId;
    private TeamId $winner;
    private TeamId $loser;
    private int $setDifference;
    private int $pointsDifference;

    public function __construct(
        GameId $gameId,
        LeagueId $leagueId,
        TeamId $winner,
        TeamId $loser,
        int $setDifference,
        int $pointsDifference
    ) {
        $this->gameId = $gameId;
        $this->leagueId = $leagueId;
        $this->winner = $winner;
        $this->loser = $loser;
        $this->setDifference = $setDifference;
        $this->pointsDifference = $pointsDifference;
    }

    public function getGameId(): GameId
    {
        return $this->gameId;
    }

    public function getLeagueId(): LeagueId
    {
        return $this->leagueId;
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

    public function getPointsDifference(): int
    {
        return $this->pointsDifference;
    }

    public function eventVersion(): int
    {
        return 1;
    }
}
