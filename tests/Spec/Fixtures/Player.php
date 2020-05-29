<?php

declare(strict_types=1);
namespace Spec\Fixtures;

use FoosTeam\Model\Player\Name;
use FoosTeam\Model\Player\Player as PlayerEntity;
use FoosCommon\Model\Player\PlayerId;
use Ramsey\Uuid\Uuid;

final class Player
{
    public static function createWithSlackHandle($slackHandle)
    {
        return new PlayerEntity(PlayerId::fromString((string)Uuid::uuid4()), Name::fromSlackHandle($slackHandle));
    }
}
