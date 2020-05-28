<?php

declare(strict_types=1);

namespace Tests\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use FoosLeague\Domain\League\GameResult;
use FoosLeague\Domain\League\Name;
use FoosLeague\Domain\League\PendingGame;
use FoosLeague\Domain\League\StartLeagueService;
use FoosLeague\Domain\League\Event\GameWasAdded;
use FoosLeague\Domain\League\League;
use FoosCommon\Model\Owner\OwnerId;
use FoosLeague\Domain\League\LeagueId;
use FoosLeague\Infrastructure\Repository\InMemoryGameRepository;
use FoosCommon\Model\Player\PlayerId;
use FoosTeam\Model\Team\Team;
use FoosCommon\Model\Team\TeamId;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class LeagueContext implements Context
{
    private League $league;
    /** @param array<string, Team> $teams */
    private array $teams;
    private InMemoryGameRepository $repository;

    private function createLeague($leagueName): void
    {
        $this->league = League::create(
            OwnerId::fromString('owner'),
            LeagueId::fromString($leagueName),
            Name::fromString($leagueName)
        );
    }

    /**
     * @When /^I create a league "([^"]*)"$/
     */
    public function iCreateALeague($leagueName)
    {
        $this->createLeague($leagueName);
    }

    /**
     * @Then /^"([^"]*)" league is created$/
     */
    public function leagueIsCreated($leagueName)
    {
        Assert::same($this->league->getName()->asString(), $leagueName);
    }

    /**
     * @Given /^A league exists$/
     */
    public function aLeagueExists()
    {
        $this->createLeague('league');
    }


    /**
     * @Given /^Team "([^"]*)" exists$/
     */
    public function teamExists($teamName)
    {
        $this->teams[$teamName] = new Team(
            TeamId::fromString((string)Uuid::uuid4()),
            [PlayerId::fromString((string)Uuid::uuid4()), PlayerId::fromString((string)Uuid::uuid4())],
            \FoosTeam\Model\Team\Name::fromString($teamName)
        );
    }

    /**
     * @When /^I add "([^"]*)" to that league$/
     */
    public function iAddToThatLeague($team)
    {
        $this->league->addTeam($this->teams[$team]->getId(), $this->teams[$team]->players());
    }

    /**
     * @Then /^"([^"]*)" is added to the league$/
     */
    public function isAddedToTheLeague($team)
    {
        try {
            $this->league->addTeam($this->teams[$team]->getId(), $this->teams[$team]->players());
        } catch (\DomainException $exception) {
            Assert::same('Team already in the league', $exception->getMessage());
        }
    }

    /**
     * @Given /^that league league has "([^"]*)"$/
     */
    public function thatLeagueLeagueHas($team)
    {
        $this->league->addTeam($this->teams[$team]->getId(), $this->teams[$team]->players());
    }

    /**
     * @When /^I remove "([^"]*)" from that league$/
     */
    public function iRemoveFromThatLeague($team)
    {
        $this->league->removeTeam($this->teams[$team]->getId());
    }

    /**
     * @Then /^"([^"]*)" is removed from that league$/
     */
    public function isRemovedFromThatLeague($team)
    {
        try {
            $this->league->removeTeam($this->teams[$team]->getId());
        } catch (\DomainException $exception) {
            Assert::same('Team is not in the league', $exception->getMessage());
        }
    }

    /**
     * @Given /^the following teams are in the league:$/
     */
    public function theFollowingTeamsAreInTheLeague(TableNode $table)
    {
        foreach ($table as $row) {
            $this->teamExists($row['name']);
        }

        foreach ($this->teams as $team) {
            $this->league->addTeam($team->getId(), $team->players());
        }
    }

    /**
     * @When /^The league is started$/
     */
    public function theLeagueIsStarted()
    {
        $gameRepository = new InMemoryGameRepository();
        $service = new StartLeagueService($gameRepository);
        $service->start($this->league);
    }

    /**
     * @Then /^the following schedule is generated$/
     */
    public function theFollowingScheduleIsGenerated(TableNode $table)
    {
        $pendingEvents = array_slice($this->league->getPendingEvents(), 5);
        foreach ($table as $row) {
            $event = array_shift($pendingEvents);
            Assert::isInstanceOf($event, GameWasAdded::class);
            Assert::eq($event->getHomeTeamId()->asString(), $this->teams[$row['Home team']]->getId()->asString());
            Assert::eq($event->getAwayTeamId()->asString(), $this->teams[$row['Away team']]->getId()->asString());
        }
    }

    /**
     * @Given /^started with (.*) teams$/
     */
    public function startedWithTeams($numberOfSignedUpTeam)
    {
        for ($i = 0; $i < $numberOfSignedUpTeam; $i++) {
            $this->teamExists('team ' . $i);
        }
        $this->repository = new InMemoryGameRepository();
        $service = new StartLeagueService($this->repository);
        foreach ($this->teams as $team) {
            $this->league->addTeam($team->getId(), $team->players());
        }
        $service->start($this->league);
    }

    /**
     * @When /^all games ended$/
     */
    public function allGamesEnded()
    {
        $games = $this->repository->all();
        foreach ($games as $game) {
            $this->league->endGame(
                new PendingGame($game->getHomeTeamId(), $game->getAwayTeamId()),
                new GameResult($game->getHomeTeamId(), $game->getAwayTeamId(), random_int(1, 2), random_int(1, 20))
            );
        }
    }

    /**
     * @Then /^I can determine (.*) playoffs bound teams$/
     */
    public function iCanDeterminePlayoffsBoundTeams($numberOfPlayoffTeams)
    {
        Assert::count($this->league->playoffBoundTeams((int)$numberOfPlayoffTeams), (int)$numberOfPlayoffTeams);
    }

    /**
     * @Given /^I can not determine less than (\d+)$/
     */
    public function iCanNotDetermineLessThan($numberOfTeams)
    {
        try {
            $this->league->playoffBoundTeams((int)$numberOfTeams - 1);
        } catch (\DomainException $e) {
            Assert::same('Can only determine 2,4 or 8 playoff bound teams', $e->getMessage());
        }
    }

    /**
     * @Given /^I can not determine more than (\d+)$/
     */
    public function iCanNotDetermineMoreThan($numberOfTeams)
    {
        try {
            $this->league->playoffBoundTeams((int)$numberOfTeams + 1);
        } catch (\DomainException $e) {
            Assert::same('Can only determine 2,4 or 8 playoff bound teams', $e->getMessage());
        }
    }
}
