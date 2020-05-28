<?php

declare(strict_types=1);

namespace FoosTeam\Model\Player;

use FoosCommon\Model\AggregateRoot;
use FoosCommon\Model\Player\PlayerId;

final class Player extends AggregateRoot
{
    private Name $name;
    private PlayerId $id;

    public function __construct(PlayerId $id, Name $name)
    {
        $this->name = $name;
        $this->id = $id;

        $this->raiseEvent(new PlayerCreatedEvent($id->asString()));
    }

    public function getName(): string
    {
        return $this->name->asString();
    }

    public function getId(): PlayerId
    {
        return $this->id;
    }
}
