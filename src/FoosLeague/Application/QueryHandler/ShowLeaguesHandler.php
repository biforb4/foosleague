<?php

declare(strict_types=1);

namespace FoosLeague\Application\QueryHandler;

use FoosLeague\Application\Dto\League;
use FoosLeague\Application\Query\ShowLeagues;
use FoosLeague\Application\Repository\LeagueListRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

final class ShowLeaguesHandler implements MessageSubscriberInterface
{
    private LeagueListRepositoryInterface $repository;

    public function __construct(LeagueListRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return array<int, League>
     */
    public function __invoke(ShowLeagues $message): array
    {
        return $this->repository->findAllLeaguesFor($message->getOwner());
    }

    public static function getHandledMessages(): iterable
    {
        yield ShowLeagues::class;
    }
}
