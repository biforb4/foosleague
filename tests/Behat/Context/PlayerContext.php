<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use Behat\Behat\Context\Context;
use FoosTeam\Model\Player\Name;
use FoosTeam\Model\Player\Player;
use FoosCommon\Model\Player\PlayerId;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

final class PlayerContext implements Context
{
    private Player $player;

    /**
     * @When /^I create player with "([^"]*)" Slack Handle$/
     */
    public function iCreatePlayerWithSlackHandle($slackHandle)
    {
        $this->player = new Player(PlayerId::fromString((string)Uuid::uuid4()), Name::fromSlackHandle($slackHandle));
    }

    /**
     * @Then /^A player is created and its name is "([^"]*)"$/
     */
    public function aPlayerIsCreatedAndItsNameIs($slackHandle)
    {
        Assert::same($slackHandle, $this->player->getName());
    }
}
