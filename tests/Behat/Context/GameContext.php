<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use Behat\Behat\Context\Context;
use FoosLeague\Domain\Game\Game;
use FoosLeague\Domain\Game\GameId;
use FoosLeague\Domain\Game\Score;
use FoosLeague\Domain\Game\Set;
use FoosLeague\Domain\League\LeagueId;
use FoosCommon\Model\Team\TeamId;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

final class GameContext implements Context
{
    /** @var array<Set> */
    private array $sets;

    /**
     * @When /^the first set score is (.*)$/
     */
    public function theFirstSetScoreIs($firstSet)
    {
        $this->setUpSet($firstSet);
    }

    /**
     * @When /^the second set score is (.*)$/
     */
    public function theSecondSetScoreIs($secondSet)
    {
        $this->setUpSet($secondSet);
    }

    /**
     * @When /^the third set score is (.*)$/
     */
    public function theThirdSetScoreIs($thirdSet)
    {
        if ($thirdSet !== 'no score') {
            $this->setUpSet($thirdSet);
        }
    }

    /**
     * @Then /^the game ends (.*)$/
     */
    public function theGameEnds($result)
    {
        $game = new Game(
            GameId::fromString((string)Uuid::uuid4()),
            LeagueId::fromString((string)Uuid::uuid4()),
            TeamId::fromString((string)Uuid::uuid4()),
            TeamId::fromString((string)Uuid::uuid4())
        );

        $game->end(Score::withSets($this->sets));

        Assert::same($game->getScoreAsString(), $result);
    }

    private function setUpSet($firstSet): void
    {
        if ($firstSet === null) {
            return;
        }
        $points = explode(':', $firstSet);
        $this->sets[] = Set::withPoints((int)$points[0], (int)$points[1]);
    }
}
