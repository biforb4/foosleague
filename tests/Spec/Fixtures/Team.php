<?php

declare(strict_types=1);

namespace Spec\Fixtures;

use FoosCommon\Model\Player\PlayerId;
use FoosTeam\Model\Team\Name;
use FoosTeam\Model\Team\Team as TeamEntity;
use FoosCommon\Model\Team\TeamId;
use Ramsey\Uuid\Uuid;

final class Team
{
    public static function createTeam(): TeamEntity
    {
        return new TeamEntity(
            TeamId::fromString((string)Uuid::uuid4()),
            [PlayerId::fromString((string)Uuid::uuid4()), PlayerId::fromString((string)Uuid::uuid4())],
            Name::fromString('name')
        );
    }
}
