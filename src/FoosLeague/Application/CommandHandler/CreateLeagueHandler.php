<?php

declare(strict_types=1);

namespace FoosLeague\Application\CommandHandler;

use FoosLeague\Application\Command\CreateLeague;
use FoosLeague\Domain\League\League;
use FoosLeague\Domain\League\LeagueRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

final class CreateLeagueHandler implements MessageSubscriberInterface
{
    private LeagueRepositoryInterface $repository;

    public function __construct(LeagueRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CreateLeague $command)
    {
        $league = League::create($command->getOwner(), $command->getId(), $command->getName());
        $this->repository->save($league);
    }

    public static function getHandledMessages(): iterable
    {
        yield CreateLeague::class;
    }
}
