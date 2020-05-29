<?php

declare(strict_types=1);

namespace tests\Spec\FoosTeam\Model\Player;

use FoosTeam\Model\Player\Name;
use FoosTeam\Model\Player\Player;
use FoosTeam\Model\Player\PlayerCreatedEvent;
use FoosCommon\Model\Player\PlayerId;
use PhpSpec\ObjectBehavior;
use Ramsey\Uuid\Uuid;

class PlayerSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(PlayerId::fromString((string)Uuid::uuid4()), Name::fromSlackHandle('player'));
    }

    public function it_can_be_created_with_name()
    {
        $this->shouldBeAnInstanceOf(Player::class);
        $this->getName()->shouldBe('player');
    }

    public function it_should_have_an_id()
    {
        $this->getId()->shouldBeAnInstanceOf(PlayerId::class);
    }

    public function it_should_raise_created_event()
    {
        $this->getEvents()->shouldBeArray();
        $this->getEvents()[0]->shouldBeAnInstanceOf(PlayerCreatedEvent::class);
        $this->getEvents()[0]->getPlayerId()->shouldBeEqualTo($this->getId()->asString());
    }
}
