<?php

declare(strict_types=1);

namespace Tests\Spec\FoosLeague\Domain\Game;

use FoosLeague\Domain\Game\Game;
use FoosLeague\Domain\Game\GameEndedEvent;
use FoosLeague\Domain\Game\GameId;
use FoosLeague\Domain\Game\Score;
use FoosLeague\Domain\Game\Set;
use FoosLeague\Domain\League\LeagueId;
use FoosCommon\Model\Team\TeamId;
use PhpSpec\ObjectBehavior;
use Ramsey\Uuid\Uuid;

class GameSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(
            GameId::fromString((string)Uuid::uuid4()),
            LeagueId::fromString((string)Uuid::uuid4()),
            TeamId::fromString((string)Uuid::uuid4()),
            TeamId::fromString((string)Uuid::uuid4())
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Game::class);
    }

    public function it_should_return_id()
    {
        $this->getId()->shouldBeAnInstanceOf(GameId::class);
    }

    public function it_can_end_with_home_team_win()
    {
        $this->end(Score::withSets([Set::withPoints(10, 1), Set::withPoints(10, 1)]));
        $this->getScoreAsString()->shouldBe('2:0 (10:1 10:1)');
    }

    public function it_can_end_with_away_team_win()
    {
        $this->end(Score::withSets([Set::withPoints(1, 10), Set::withPoints(10, 9), Set::withPoints(9, 10)]));
        $this->getScoreAsString()->shouldBe('1:2 (1:10 10:9 9:10)');
    }

    public function it_should_raise_game_ended_event()
    {
        $leagueId = LeagueId::fromString((string)Uuid::uuid4());
        $homeTeam = TeamId::fromString((string)Uuid::uuid4());
        $awayTeam = TeamId::fromString((string)Uuid::uuid4());
        $this->beConstructedWith(
            GameId::fromString((string)Uuid::uuid4()),
            $leagueId,
            $homeTeam,
            $awayTeam
        );

        $this->end(Score::withSets([Set::withPoints(10, 1), Set::withPoints(10, 1)]));
        $this->getEvents()->shouldBeArray();
        $this->getEvents()->shouldHaveCount(1);
        $event = $this->getEvents()[0];
        $event->shouldBeAnInstanceOf(GameEndedEvent::class);
        $event->getGameId()->shouldBe($this->getId());
        $event->getLeagueId()->shouldBe($leagueId);
        $event->getWinner()->shouldBe($homeTeam);
        $event->getLoser()->shouldBe($awayTeam);
        $event->getSetDifference()->shouldBe(2);
        $event->getPointsDifference()->shouldBe(18);
    }
}
