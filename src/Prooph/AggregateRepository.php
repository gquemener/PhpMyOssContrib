<?php
declare(strict_types=1);

namespace App\Prooph;

use Amp\Promise;
use Amp\Success;
use Prooph\EventStore\Async\EventStoreConnection;
use Prooph\EventStore\Internal\Consts;
use Prooph\EventStore\SliceReadStatus;
use Prooph\EventStore\UserCredentials;
use function Amp\call;
use Prooph\EventStore\ExpectedVersion;

class AggregateRepository
{
    private EventStoreConnection $eventStoreConnection;

    private MessageTransformer $transformer;

    private string $streamCategory;

    private string $aggregateRootClassName;

    private bool $optimisticConcurrency;

    public function __construct(
        EventStoreConnection $eventStoreConnection,
        MessageTransformer $transformer,
        string $streamCategory,
        string $aggregateRootClassName,
        bool $useOptimisticConcurrencyByDefault = true
    ) {
        $this->eventStoreConnection = $eventStoreConnection;
        $this->transformer = $transformer;
        $this->streamCategory = $streamCategory;
        $this->aggregateRootClassName = $aggregateRootClassName;
        $this->optimisticConcurrency = $useOptimisticConcurrencyByDefault;
    }

    protected function getAggregateRoot(
        string $aggregateId,
        UserCredentials $credentials = null
    ): Promise {
        return call(function () use ($aggregateId, $credentials) {
            $stream = $this->streamCategory . '-' . $aggregateId;

            $start = 0;
            $count = Consts::MAX_READ_SIZE;

            do {
                $events = [];

                $streamEventsSlice = yield $this->eventStoreConnection
                    ->readStreamEventsForwardAsync(
                        $stream,
                        $start,
                        $count,
                        true,
                        $credentials
                    );

                if (!$streamEventsSlice->status()->equals(
                    SliceReadStatus::success())
                ) {
                    return null;
                }

                $start = $streamEventsSlice->nextEventNumber();

                foreach ($streamEventsSlice->events() as $event) {
                    $events[] = $this->transformer->toDomainEvent($event);
                }

                if (isset($aggregateRoot)) {
                    assert($aggregateRoot instanceof AggregateRoot);
                    $aggregateRoot->replay($events);
                } else {
                    $className = $this->aggregateRootClassName;
                    $aggregateRoot = $className::reconstituteFromHistory($events);
                }
            } while (!$streamEventsSlice->isEndOfStream());

            $aggregateRoot->setExpectedVersion(
                $streamEventsSlice->lastEventNumber()
            );

            return $aggregateRoot;
        });
    }

    protected function saveAggregateRoot(
        AggregateRoot $aggregateRoot,
        int $expectedVersion = null,
        UserCredentials $credentials = null
    ): Promise {
        return call(function () use ($aggregateRoot, $expectedVersion, $credentials) {
            $domainEvents = $aggregateRoot->popRecordedEvents();

            if (empty($domainEvents)) {
                return new Success();
            }

            $aggregateId = $aggregateRoot->aggregateId();
            $stream = $this->streamCategory . '-' . $aggregateId;

            $eventData = [];

            foreach ($domainEvents as $event) {
                $eventData[] = $this->transformer->toEventData($event);
            }

            if (null === $expectedVersion) {
                $expectedVersion = $this->optimisticConcurrency
                    ? $aggregateRoot->expectedVersion()
                    : ExpectedVersion::ANY;
            }

            $writeResult = yield $this->eventStoreConnection
                ->appendToStreamAsync(
                    $stream,
                    $expectedVersion,
                    $eventData,
                    $credentials
                );

            $aggregateRoot->setExpectedVersion(
                $writeResult->nextExpectedVersion()
            );

            return $aggregateRoot;
        });
    }
}
