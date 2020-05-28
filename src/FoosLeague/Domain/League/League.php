<?php

declare(strict_types=1);

namespace FoosLeague\Domain\League;

use DomainException;
use FoosCommon\Model\DomainEvent;
use FoosCommon\Model\EventSourcedAggregateRoot;
use FoosCommon\Model\Owner\OwnerId;
use FoosLeague\Domain\League\Event\GameWasAdded;
use FoosLeague\Domain\League\Event\LeagueGameWasEnded;
use FoosLeague\Domain\League\Event\LeagueWasCreated;
use FoosLeague\Domain\League\Event\LossWasRecorded;
use FoosLeague\Domain\League\Event\TeamWasAdded;
use FoosLeague\Domain\League\Event\TeamWasRemoved;
use FoosLeague\Domain\League\Event\WinWasRecorded;
use FoosLeague\Domain\League\Policy\DefaultSorting;
use FoosLeague\Domain\League\Policy\SortingRankingsStrategyInterface;
use FoosCommon\Model\Player\PlayerId;
use FoosCommon\Model\Team\TeamId;
use FoosLeague\Infrastructure\EventSourcing\EventStream;
use InvalidArgumentException;

final class League extends EventSourcedAggregateRoot
{
    private OwnerId $owner;
    private LeagueId $leagueId;
    /** @var array<int, TeamId> */
    private array $teams = [];
    private Name $name;
    /** @var array<int, PlayerId> */
    private array $players = [];
    private Schedule $schedule;
    private Rankings $rankings;
    private SortingRankingsStrategyInterface $sortingRankingsStrategy;

    public static function create(OwnerId $owner, LeagueId $id, Name $name): League
    {
        $event = new LeagueWasCreated($owner, $id, $name);
        $league = new self([$event], 1, new DefaultSorting());
        $league->apply($event);

        return $league;
    }

    public static function fromEventStream(EventStream $eventStream): League
    {
        return new self($eventStream->getEvents(), $eventStream->getVersion(), new DefaultSorting());
    }

    /** @param array<int, DomainEvent> $domainEvents */
    private function __construct(
        array $domainEvents,
        int $streamVersion,
        SortingRankingsStrategyInterface $sortingRankingsStrategy
    ) {
        $this->schedule = new Schedule();
        $this->rankings = new Rankings();
        $this->sortingRankingsStrategy = $sortingRankingsStrategy;
        parent::__construct($domainEvents, $streamVersion);
    }

    public function getLeagueId(): LeagueId
    {
        return $this->leagueId;
    }

    public function getOwner(): OwnerId
    {
        return $this->owner;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @param array<int, PlayerId> $playerIds
     */
    public function addTeam(TeamId $teamId, array $playerIds): void
    {
        if (in_array($teamId, $this->teams, false)) {
            throw new DomainException('Team already in the league');
        }

        if (count(array_intersect($this->players, $playerIds)) > 1) {
            throw new DomainException('Trying to add already added player');
        }

        $event = new Event\TeamWasAdded($this->leagueId, $teamId, $playerIds);
        $this->apply($event);
    }

    public function removeTeam(TeamId $teamId): void
    {
        if (!in_array($teamId, $this->teams, false)) {
            throw new DomainException('Team is not in the league');
        }

        $this->apply(new TeamWasRemoved($this->leagueId, $teamId));
    }

    private function addGame(PendingGame $game): void
    {
        $this->apply(new GameWasAdded($this->leagueId, $game->getHomeTeamId(), $game->getAwayTeamId()));
    }

    /**
     * @return array<int, TeamId>
     */
    public function getSignedUpTeams(): array
    {
        return $this->teams;
    }

    public function startWithGames(PendingGame ...$pendingGames): void
    {
        if (count($this->teams) < 3) {
            throw new DomainException('Cannot start with less than 3 teams');
        }

        if (!$this->everyTeamWillPlayEachOtherOnce($pendingGames)) {
            throw new DomainException('Could not start a league without pending games for combination of all teams');
        }
        foreach ($pendingGames as $pendingGame) {
            $this->addGame($pendingGame);
        }
    }

    public function endGame(PendingGame $pendingGame, GameResult $gameResult): void
    {
        $this->apply(new LeagueGameWasEnded($pendingGame));
        $this->apply(new WinWasRecorded(
            $gameResult->getWinner(),
            $gameResult->getSetDifference(),
            $gameResult->getGoalDifference()
        ));
        $this->apply(new LossWasRecorded(
            $gameResult->getLoser(),
            -$gameResult->getSetDifference(),
            -$gameResult->getGoalDifference()
        ));
    }

    /**
     * @return array<array-key, TeamId>
     */
    public function playoffBoundTeams(int $howMany): array
    {
        if (!in_array($howMany, [2, 4, 8])) {
            throw new DomainException('Can only determine 2,4 or 8 playoff bound teams');
        }

        return $this->rankings->best($howMany, $this->sortingRankingsStrategy);
    }

    protected function when(DomainEvent $event): void
    {
        switch (true) {
            case $event instanceof LeagueWasCreated:
                $this->whenLeagueCreated($event);
                break;
            case $event instanceof TeamWasAdded:
                $this->whenTeamWasAdded($event);
                break;
            case $event instanceof TeamWasRemoved:
                $this->whenTeamWasRemoved($event);
                break;
            case $event instanceof GameWasAdded:
                $this->whenGameWasAdded($event);
                break;
            case $event instanceof LeagueGameWasEnded:
                $this->whenLeagueGameWasEnded($event);
                break;
            case $event instanceof LossWasRecorded:
                $this->whenLossWasRecorded($event);
                break;
            case $event instanceof WinWasRecorded:
                $this->whenWinWasRecorded($event);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Event %s not supported', get_class($event)));
        }
    }

    private function whenLeagueCreated(LeagueWasCreated $event): void
    {
        $this->owner = $event->getOwner();
        $this->leagueId = $event->getLeagueId();
        $this->name = $event->getName();
    }

    private function whenTeamWasAdded(TeamWasAdded $event): void
    {
        $this->teams[] = $event->getTeamId();
        $this->players = array_merge($this->players, $event->getPlayerIds());
    }

    private function whenTeamWasRemoved(TeamWasRemoved $event): void
    {
        $teamId = $event->getTeamId();
        foreach ($this->teams as $index => $existingTeam) {
            if ($teamId->asString() === $existingTeam->asString()) {
                unset($this->teams[$index]);
                reset($this->teams);
                break;
            }
        }
    }

    private function whenGameWasAdded(GameWasAdded $event): void
    {
        $this->schedule->addGame(new PendingGame($event->getHomeTeamId(), $event->getAwayTeamId()));
    }

    /**
     * @param PendingGame[] $pendingGames
     */
    private function everyTeamWillPlayEachOtherOnce(array $pendingGames): bool
    {
        $teamsGameCount = [];
        foreach ($this->teams as $teamId) {
            $teamsGameCount[$teamId->asString()] = 0;
        }


        foreach ($pendingGames as $pendingGame) {
            $teamsGameCount[$pendingGame->getHomeTeamId()->asString()]++;

            $teamsGameCount[$pendingGame->getAwayTeamId()->asString()]++;
        }

        $plannedGames = count($this->teams) - 1;
        foreach ($teamsGameCount as $value) {
            if ($value !== $plannedGames) {
                return false;
            }
        }

        return true;
    }

    private function whenLeagueGameWasEnded(LeagueGameWasEnded $event): void
    {
        $this->schedule->endGame($event->getPendingGame());
    }

    private function whenLossWasRecorded(LossWasRecorded $event): void
    {
        $this->rankings->recordLoss($event->getTeamId(), $event->getSets(), $event->getGoals());
    }

    private function whenWinWasRecorded(WinWasRecorded $event): void
    {
        $this->rankings->recordWin($event->getTeamId(), $event->getSets(), $event->getGoals());
    }
}
