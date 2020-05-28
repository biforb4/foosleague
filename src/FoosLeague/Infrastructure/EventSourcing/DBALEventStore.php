<?php

declare(strict_types=1);

namespace FoosLeague\Infrastructure\EventSourcing;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use FoosCommon\Model\DomainEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

class DBALEventStore implements EventStoreInterface
{
    private const SERIALIZED_FORMAT = 'json';
    private Connection $connection;
    private MessageBusInterface $eventBus;
    private SerializerInterface $serializer;

    public function __construct(Connection $connection, MessageBusInterface $eventBus, SerializerInterface $serializer)
    {
        $this->connection = $connection;
        $this->eventBus = $eventBus;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function appendWith(EventStreamId $eventStreamId, array $events): void
    {
        $this->connection->beginTransaction();

        try {
            $index = 0;
            foreach ($events as $event) {
                $this->appendEventStore($eventStreamId, $index, $event);
            }
            $this->connection->commit();
            $this->dispatch($events);
        } catch (\Throwable $exception) {
            try {
                $this->connection->rollBack();
            } catch (\Throwable $exception) {
                //ignore
            }

            throw new EventStoreAppendException(
                sprintf('Could not append to event store: %s', $exception->getMessage()),
                0,
                $exception
            );
        }
    }

    public function eventStreamFor(EventStreamId $eventStreamId): EventStream
    {
        try {
            $queryBuilder = $this->connection->createQueryBuilder();
            $queryBuilder->select('stream_version, event_type, event_body')
                ->from('event_store')
                ->where('stream_name = ?')
                ->orderBy('stream_version')
                ->setParameters([$eventStreamId->getStreamName()]);

            /** @var Statement $statement */
            $statement = $queryBuilder->execute();
            $result = $statement->fetchAll();

            return $this->buildEventStream($result);
        } catch (\Exception $exception) {
            throw new EventStoreException(
                sprintf(
                    'Cannot query event stream for: %s. Reason: %s',
                    $eventStreamId->getStreamName(),
                    $exception->getMessage()
                ),
                0,
                $exception
            );
        }
    }

    private function appendEventStore(EventStreamId $eventStreamId, int $index, DomainEvent $event): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->insert('event_store')
            ->values([
                'event_body' => '?',
                'event_type' => '?',
                'stream_name' => '?',
                'stream_version' => '?'
            ])
            ->setParameters([
                $this->serializer->serialize($event, self::SERIALIZED_FORMAT),
                \get_class($event),
                $eventStreamId->getStreamName(),
                $eventStreamId->getStreamVersion() + $index
            ]);

        $queryBuilder->execute();
    }

    private function buildEventStream(array $resultSet): EventStream
    {
        $events = [];
        $version = 0;

        /** @var array{event_body: string, event_type: string, stream_version:int} $eventData */
        foreach ($resultSet as $eventData) {
            /** @var DomainEvent $event */
            $event = $this->serializer->deserialize(
                $eventData['event_body'],
                $eventData['event_type'],
                self::SERIALIZED_FORMAT
            );
            $events[] = $event;
            $version = $eventData['stream_version'];
        }

        return new EventStream($events, $version);
    }

    /** @param array<int,DomainEvent> $events */
    private function dispatch(array $events): void
    {
        foreach ($events as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
