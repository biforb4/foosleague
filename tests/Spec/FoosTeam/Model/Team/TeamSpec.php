<?php

declare(strict_types=1);

namespace tests\Spec\FoosTeam\Model\Team;

use FoosCommon\Model\Player\PlayerId;
use FoosTeam\Model\Team\Name;
use FoosTeam\Model\Team\Team;
use FoosTeam\Model\Team\TeamCreatedEvent;
use FoosCommon\Model\Team\TeamId;
use PhpSpec\ObjectBehavior;
use Ramsey\Uuid\Uuid;

class TeamSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(
            TeamId::fromString((string)Uuid::uuid4()),
            [PlayerId::fromString((string)Uuid::uuid4()), PlayerId::fromString((string)Uuid::uuid4())],
            Name::fromString('team')
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Team::class);
    }

    public function it_should_have_name()
    {
        $this->getName()->shouldBeAnInstanceOf(Name::class);
    }

    public function it_should_have_exactly_two_players()
    {
        $this->players()->shouldHaveCount(2);
        foreach ($this->players() as $playerId) {
            $playerId->shouldBeAnInstanceOf(PlayerId::class);
        }
    }

    public function it_should_throw_when_creating_with_less_than_two_players()
    {
        $this
            ->shouldThrow(new \InvalidArgumentException('A team requires exactly two players'))
            ->during(
                '__construct',
                [
                    TeamId::fromString((string)Uuid::uuid4()),
                    [
                        PlayerId::fromString((string)Uuid::uuid4())
                    ],
                    Name::fromString('team')
                ]
            );
    }

    public function it_should_throw_when_creating_with_more_than_two_players()
    {
        $this
            ->shouldThrow(new \InvalidArgumentException('A team requires exactly two players'))
            ->during(
                '__construct',
                [
                    TeamId::fromString((string)Uuid::uuid4()),
                    [
                        PlayerId::fromString((string)Uuid::uuid4()),
                        PlayerId::fromString((string)Uuid::uuid4()),
                        PlayerId::fromString((string)Uuid::uuid4())
                    ],
                    Name::fromString('team')
                ]
            );
    }

    public function it_should_raise_created_event()
    {
        $this->getEvents()->shouldBeArray();
        $this->getEvents()[0]->shouldBeAnInstanceOf(TeamCreatedEvent::class);
        $this->getEvents()[0]->getTeamId()->shouldBeEqualTo($this->getId()->asString());
    }

    public function it_should_throw_when_players_are_not_unique()
    {
        $player = PlayerId::fromString((string)Uuid::uuid4());
        $this
            ->shouldThrow(new \InvalidArgumentException('Every player has to be unique'))
            ->during(
                '__construct',
                [
                    TeamId::fromString((string)Uuid::uuid4()),
                    [
                        $player,
                        $player,
                    ],
                    Name::fromString('team')
                ]
            );
    }
}
