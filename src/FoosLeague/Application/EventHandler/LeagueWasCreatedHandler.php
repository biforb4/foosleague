<?php

declare(strict_types=1);

namespace FoosLeague\Application\EventHandler;

use FoosLeague\Application\Dto\League;
use FoosLeague\Application\Repository\LeagueListRepositoryInterface;
use FoosLeague\Domain\League\Event\LeagueWasCreated;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

final class LeagueWasCreatedHandler implements MessageSubscriberInterface
{
    private LeagueListRepositoryInterface $repository;

    public function __construct(LeagueListRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(LeagueWasCreated $event)
    {
        $this->repository->save(League::fromLeagueWasCreatedEvent($event));
    }

    public static function getHandledMessages(): iterable
    {
        yield LeagueWasCreated::class;
    }
}
