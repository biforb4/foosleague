<?php

declare(strict_types=1);

namespace FoosLeague\Infrastructure\ReadModel;

use Doctrine\DBAL\Connection;
use FoosLeague\Application\Dto\League;
use FoosLeague\Application\Repository\LeagueListRepositoryInterface;
use FoosCommon\Model\Owner\OwnerId;

final class LeagueListListRepository implements LeagueListRepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /** @return array<int,League> */
    public function findAllLeaguesFor(OwnerId $owner): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('league_name, owner_id')
            ->from('league_list')
            ->where('owner_id = :ownerId');

        $qb->setParameter('ownerId', $owner->asString());

        /**
         * @psalm-suppress InvalidScalarArgument
         * @psalm-suppress PossiblyInvalidMethodCall
         * @var array<int, League>
         */
        return $qb->execute()->fetchAll(
            \PDO::FETCH_FUNC,
            'FoosLeague\Application\Dto\League::fromPdo'
        );
    }

    public function save(League $league): bool
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->insert('league_list')
            ->values([
                'owner_id' => '?',
                'league_name' => '?'
            ])
            ->setParameters([$league->getOwnerId(), $league->getName()]);

        return (bool) $qb->execute();
    }
}
