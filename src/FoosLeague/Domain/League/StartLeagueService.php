<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League;

use FoosLeague\Domain\Game\Game;
use FoosLeague\Domain\Game\GameId;
use FoosLeague\Domain\Game\GameRepositoryInterface;
use FoosCommon\Model\Team\TeamId;
use Ramsey\Uuid\Uuid;

final class StartLeagueService
{
    private GameRepositoryInterface $repository;

    public function __construct(GameRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function start(League $league): void
    {
        $teams = $league->getSignedUpTeams();

        /** @var Game $game */
        $generatedGames = $this->generateGames($league->getLeagueId(), $teams);
        $pendingGames = [];
        foreach ($generatedGames as $game) {
            $this->repository->add($game);
            $pendingGames[] = new PendingGame($game->getHomeTeamId(), $game->getAwayTeamId());
        }

        $league->startWithGames(...$pendingGames);
    }

    /**
     * @param array<int, TeamId> $teams
     * @return array<int, Game>
     */
    private function generateGames(LeagueId $leagueId, array $teams): array
    {
        $games = [];
        $homeTeam = array_shift($teams);
        foreach ($teams as $awayTeam) {
            $games[] = new Game(GameId::fromString((string)Uuid::uuid4()), $leagueId, $homeTeam, $awayTeam);
        }
        if (count($teams) > 1) {
            $games = array_merge($games, $this->generateGames($leagueId, $teams));
        }
        return $games;
    }
}
