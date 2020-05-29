<?php

declare(strict_types=1);

namespace Spec\FoosLeague\Domain\League;

use FoosLeague\Domain\League\Event\GameWasAdded;
use FoosLeague\Domain\League\Event\LeagueGameWasEnded;
use FoosLeague\Domain\League\Event\LossWasRecorded;
use FoosLeague\Domain\League\Event\LeagueWasCreated;
use FoosLeague\Domain\League\Event\TeamWasAdded;
use FoosLeague\Domain\League\Event\TeamWasRemoved;
use FoosLeague\Domain\League\Event\WinWasRecorded;
use FoosLeague\Domain\League\GameResult;
use FoosLeague\Domain\League\LeagueException;
use FoosLeague\Domain\League\Name;
use FoosLeague\Domain\League\PendingGame;
use FoosCommon\Model\Team\TeamId;
use PhpSpec\Exception\Example\FailureException;
use Ramsey\Uuid\Uuid;
use FoosCommon\Model\Owner\OwnerId;
use FoosLeague\Domain\League\LeagueId;
use PhpSpec\ObjectBehavior;
use Spec\Fixtures\Team;

class LeagueSpec extends ObjectBehavior
{
    public function let()
    {
        $owner = OwnerId::fromString('id');
        $id = LeagueId::fromString('Office League');
        $name = Name::fromString('name');
        $this->beConstructedThrough('create', [$owner, $id, $name]);
    }

    public function it_should_raise_created_event()
    {
        $this->getPendingEvents()->shouldRaiseEvent(
            LeagueWasCreated::class,
            [
                'getLeagueId' => $this->getLeagueId(),
                'getOwner' => $this->getOwner(),
                'getName' => $this->getName()
            ]
        );
    }

    public function it_should_have_a_name()
    {
        $this->getName()->shouldBeAnInstanceOf(Name::class);
    }

    public function it_can_add_a_team()
    {
        $team = Team::createTeam();
        $this->addTeam($team->getId(), $team->players());
    }

    public function it_should_raise_team_added_event()
    {
        $team = Team::createTeam();
        $this->addTeam($team->getId(), $team->players());

        $this->getPendingEvents()->shouldBeArray();
        $this->getPendingEvents()->shouldHaveCount(2);
        $this->getPendingEvents()[1]->shouldBeAnInstanceOf(TeamWasAdded::class);
        $this->getPendingEvents()[1]->getLeagueId()->shouldBe($this->getLeagueId());
        $this->getPendingEvents()[1]->getTeamId()->shouldBe($team->getId());
        foreach ($team->players() as $playersId) {
            $this->getPendingEvents()[1]->getPlayerIds()->shouldContain($playersId);
        }
    }

    public function it_should_throw_when_trying_to_add_already_added_team()
    {
        $team = Team::createTeam();
        $this->addTeam($team->getId(), $team->players());
        $this->shouldThrow(new \DomainException('Team already in the league'))->during(
            'addTeam',
            [$team->getId(), $team->players()]
        );
    }

    public function it_should_throw_when_trying_to_add_already_added_player()
    {
        $team = Team::createTeam();
        $team2 = Team::createTeam();
        $this->addTeam($team->getId(), $team->players());
        $this->shouldThrow(new \DomainException('Trying to add already added player'))->during(
            'addTeam',
            [$team2->getId(), $team->players()]
        );
    }


    public function it_can_remove_existing_team()
    {
        $team = Team::createTeam();
        $this->addTeam($team->getId(), $team->players());
        $this->removeTeam($team->getId());
    }

    public function it_should_throw_exception_when_removing_non_exising_team()
    {
        $this->shouldThrow(new \DomainException('Team is not in the league'))
            ->during('removeTeam', [TeamId::fromString((string)Uuid::uuid4())]);
    }

    public function it_should_raise_team_removed_event()
    {
        $team = Team::createTeam();
        $this->addTeam($team->getId(), $team->players());
        $this->removeTeam($team->getId());

        $this->getPendingEvents()->shouldBeArray();
        $this->getPendingEvents()->shouldHaveCount(3);
        $this->getPendingEvents()[2]->shouldBeAnInstanceOf(TeamWasRemoved::class);
        $this->getPendingEvents()[2]->getLeagueId()->shouldBe($this->getLeagueId());
        $this->getPendingEvents()[2]->getTeamId()->shouldBe($team->getId());
    }

    public function it_can_return_signed_up_teams()
    {
        $this->addTeams(2);

        $this->getSignedUpTeams()->shouldBeAnArrayOfTeamIds();
    }

    public function it_can_start()
    {
        $addedTeams = $this->addTeams(3);

        $games[] = new PendingGame($addedTeams[0]->getId(), $addedTeams[1]->getId());
        $games[] = new PendingGame($addedTeams[0]->getId(), $addedTeams[2]->getId());
        $games[] = new PendingGame($addedTeams[1]->getId(), $addedTeams[2]->getId());

        $this->startWithGames(...$games);
        $this->getPendingEvents()->shouldRaiseEvent(
            GameWasAdded::class,
            [
                'getHomeTeamId' => [$addedTeams[0]->getId(), $addedTeams[0]->getId(), $addedTeams[1]->getId()],
                'getAwayTeamId' => [$addedTeams[1]->getId(), $addedTeams[2]->getId(), $addedTeams[2]->getId()]
            ],
            3
        );
    }

    public function it_cant_start_with_less_than_3_teams()
    {
        /** @var \FoosLeague\Domain\Team\Team[] $addedTeams * */
        $addedTeams = $this->addTeams(2);

        $games[] = new PendingGame($addedTeams[0]->getId(), $addedTeams[1]->getId());

        $this->shouldThrow(new \DomainException('Cannot start with less than 3 teams'))
            ->during('startWithGames', [...$games]);
    }

    public function it_cant_start_when_every_team_does_not_play_each_other()
    {
        /** @var \FoosLeague\Domain\Team\Team[] $addedTeams * */
        $addedTeams = $this->addTeams(3);

        $games[] = new PendingGame($addedTeams[0]->getId(), $addedTeams[1]->getId());
        $games[] = new PendingGame($addedTeams[0]->getId(), $addedTeams[2]->getId());

        $this->shouldThrow(new \DomainException('Could not start a league without pending games for combination of all teams'))
            ->during('startWithGames', [...$games]);
    }

    public function it_can_end_game()
    {
        $addedTeams = $this->addTeams(3);

        $games[] = new PendingGame($addedTeams[0]->getId(), $addedTeams[1]->getId());
        $games[] = new PendingGame($addedTeams[0]->getId(), $addedTeams[2]->getId());
        $games[] = new PendingGame($addedTeams[1]->getId(), $addedTeams[2]->getId());

        $this->startWithGames(...$games);
        $setDifference = random_int(1, 2);
        $pointsDifference = random_int(1, 20);
        $this->endGame(
            $games[0],
            new GameResult($games[0]->getHomeTeamId(), $games[0]->getAwayTeamId(), $setDifference, $pointsDifference)
        );
        $this->getPendingEvents()->shouldRaiseEvent(
            LossWasRecorded::class,
            [
                'getTeamId' => [$games[0]->getAwayTeamId()],
                'getSets' => [-$setDifference],
                'getGoals' => [-$pointsDifference]
            ],
            1
        );

        $this->getPendingEvents()->shouldRaiseEvent(
            WinWasRecorded::class,
            [
                'getTeamId' => [$games[0]->getHomeTeamId()],
                'getSets' => [$setDifference],
                'getGoals' => [$pointsDifference]
            ],
            1
        );

        $this->getPendingEvents()->shouldRaiseEvent(
            LeagueGameWasEnded::class,
            [
                'getPendingGame' => $games[0],
            ]
        );
    }

    public function it_can_determine_playoff_bound_teams(): void
    {
        $addedTeams = $this->addTeams(3);

        $games[] = new PendingGame($addedTeams[0]->getId(), $addedTeams[1]->getId());
        $games[] = new PendingGame($addedTeams[0]->getId(), $addedTeams[2]->getId());
        $games[] = new PendingGame($addedTeams[1]->getId(), $addedTeams[2]->getId());

        $this->startWithGames(...$games);
        $this->endGame(
            $games[0],
            new GameResult($games[0]->getHomeTeamId(), $games[0]->getAwayTeamId(), 2, 11)
        );
        $this->endGame(
            $games[1],
            new GameResult($games[1]->getAwayTeamId(), $games[1]->getHomeTeamId(), 2, 10)
        );
        $this->endGame(
            $games[2],
            new GameResult($games[2]->getHomeTeamId(), $games[2]->getAwayTeamId(), 2, 10)
        );

        $this->playoffBoundTeams(2)->shouldHaveCount(2);
        $this->playoffBoundTeams(2)->shouldHaveKeyWithValue(0, $addedTeams[0]->getId());
        $this->playoffBoundTeams(2)->shouldHaveKeyWithValue(1, $addedTeams[2]->getId());
    }

    public function it_can_not_end_non_existing_game()
    {
        $homeTeam = TeamId::fromString((string)Uuid::uuid4());
        $awayTeam = TeamId::fromString((string)Uuid::uuid4());
        $this->shouldThrow(LeagueException::pendingGameNotFound())
            ->during(
                'endGame',
                [
                    new PendingGame($homeTeam, $awayTeam),
                    new GameResult($homeTeam, $awayTeam, 2, 10)
                ]
            );
    }

    public function it_cant_determine_less_than_2_more_than_8_playoff_teams(): void
    {
        $this->shouldThrow(new \DomainException('Can only determine 2,4 or 8 playoff bound teams'))
            ->during('playoffBoundTeams', [1]);
        $this->shouldThrow(new \DomainException('Can only determine 2,4 or 8 playoff bound teams'))
            ->during('playoffBoundTeams', [3]);
        $this->shouldThrow(new \DomainException('Can only determine 2,4 or 8 playoff bound teams'))
            ->during('playoffBoundTeams', [5]);
        $this->shouldThrow(new \DomainException('Can only determine 2,4 or 8 playoff bound teams'))
            ->during('playoffBoundTeams', [6]);
        $this->shouldThrow(new \DomainException('Can only determine 2,4 or 8 playoff bound teams'))
            ->during('playoffBoundTeams', [7]);
        $this->shouldThrow(new \DomainException('Can only determine 2,4 or 8 playoff bound teams'))
            ->during('playoffBoundTeams', [9]);
    }

    public function getMatchers(): array
    {
        return [
            'beAnArrayOfTeamIds' => static function ($teamIds) {
                foreach ($teamIds as $teamId) {
                    if (!$teamId instanceof TeamId) {
                        return false;
                    }
                }
                return true;
            },
            'raiseEvent' => static function ($pendingEvents, $eventClass, $payload, $targetCount = 1) {
                $count = 0;
                foreach ($pendingEvents as $event) {
                    if ($event instanceof $eventClass) {
                        foreach ($payload as $getter => $value) {
                            if (!is_array($value)) {
                                $value = [$value];
                            }
                            if (!method_exists($event, $getter) || $event->$getter() !== $value[$count]) {
                                continue 2;
                            }
                        }
                        $count++;
                    }
                }

                if ($count === $targetCount) {
                    return true;
                }
                throw new FailureException(
                    sprintf('%s was not raised %s time(s) with specified payload', $eventClass, $targetCount)
                );
            }
        ];
    }

    private function addTeams(int $howMany): array
    {
        /** @var \FoosLeague\Domain\Team\Team[] $addedTeams * */
        $addedTeams = [];
        for ($i = 0; $i < $howMany; $i++) {
            $team = Team::createTeam();
            $this->addTeam($team->getId(), $team->players());
            $addedTeams[] = $team;
        }
        return $addedTeams;
    }
}
