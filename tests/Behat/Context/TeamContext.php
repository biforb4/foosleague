<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use Behat\Behat\Context\Context;
use FoosCommon\Model\Player\PlayerId;
use FoosTeam\Model\Team\Name;
use FoosTeam\Model\Team\Team;
use FoosCommon\Model\Team\TeamId;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class TeamContext implements Context
{
    private Team $team;
    /**
     * @When /^I create a "([^"]*)" team$/
     */
    public function iCreateATeam($name)
    {
        $this->team = new Team(
            TeamId::fromString((string)Uuid::uuid4()),
            [PlayerId::fromString((string)Uuid::uuid4()), PlayerId::fromString((string)Uuid::uuid4())],
            Name::fromString($name)
        );
    }

    /**
     * @Then /^It's name is "([^"]*)"$/
     */
    public function itsNameIs($name)
    {
        Assert::same($this->team->getName()->asString(), Name::fromString($name)->asString());
    }

    /**
     * @Then /^Has two players$/
     */
    public function hasTwoPlayers()
    {
        Assert::count($this->team->players(), 2);
    }
}
