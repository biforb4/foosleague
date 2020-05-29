<?php

declare(strict_types=1);

namespace Spec\FoosLeague\Domain\League;

use FoosLeague\Domain\Game\Game;
use FoosLeague\Domain\Game\GameRepositoryInterface;
use FoosLeague\Domain\League\Name;
use FoosLeague\Domain\League\StartLeagueService;
use FoosLeague\Domain\League\League;
use FoosLeague\Domain\League\LeagueId;
use FoosCommon\Model\Owner\OwnerId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;
use Spec\Fixtures\Team;

class StartLeagueServiceSpec extends ObjectBehavior
{
    public function let(GameRepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(StartLeagueService::class);
    }

    public function it_can_generate_games_for_league(GameRepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
        $league = League::create(
            OwnerId::fromString('owner'),
            LeagueId::fromString((string)Uuid::uuid4()),
            Name::fromString('name')
        );
        for ($i = 0; $i < 4; $i++) {
            $team = Team::createTeam();
            $league->addTeam($team->getId(), $team->players());
        }
        $this->start($league);
        $repository->add(Argument::type(Game::class))->shouldBeCalledTimes(6);
    }
}
