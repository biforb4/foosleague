<?php

declare(strict_types=1);

namespace FoosTeam\Model\Team;

use FoosCommon\Model\AggregateRoot;
use FoosCommon\Model\Team\TeamId;
use FoosCommon\Model\Player\PlayerId;
use Webmozart\Assert\Assert;

final class Team extends AggregateRoot
{
    private TeamId $id;
    /** @var array<int, PlayerId> */
    private array $playersIds;
    private Name $name;

    /** @param array<int, PlayerId> $playersIds */
    public function __construct(TeamId $id, array $playersIds, Name $name)
    {
        Assert::count($playersIds, 2, 'A team requires exactly two players');
        Assert::uniqueValues($playersIds, 'Every player has to be unique');
        $this->id = $id;
        $this->playersIds = $playersIds;
        $this->name = $name;

        $this->raiseEvent(new TeamCreatedEvent($id->asString()));
    }

    public function getId(): TeamId
    {
        return $this->id;
    }

    /** @return array<int, PlayerId> */
    public function players(): array
    {
        return $this->playersIds;
    }

    public function getName(): Name
    {
        return $this->name;
    }
}
