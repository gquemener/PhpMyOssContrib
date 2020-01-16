<?php
declare(strict_types=1);

namespace App\Contribution\Infrastructure\Repository;

use App\Contribution\Domain\Repository\ContributionRepository;
use App\Contribution\Domain\Model\ContributionId;
use App\Contribution\Domain\Model\Contribution;
use Prooph\EventStore\Pdo\PdoEventStore;
use Prooph\EventStore\StreamName;
use Prooph\EventStore\Exception\StreamNotFound;
use App\Prooph\DomainEvent;
use Prooph\EventStore\Stream;
use Prooph\Common\Messaging\DomainMessage;

final class PdoEventStoreContributionRepository implements ContributionRepository
{
    private $eventStore;

    private $identityMap = [];

    public function __construct(PdoEventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function find(ContributionId $id): ?Contribution
    {
        $contributionId = $id->toString();
        if (isset($this->identityMap[$contributionId])) {
            return $this->identityMap[$contributionId];
        }

        $streamName = $this->streamName($id);

        try {
            $events = $this->eventStore->load($streamName);
        } catch (StreamNotFound $e) {
            return null;
        }

        if (!$events->valid()) {
            return null;
        }

        /** @var Contribution */
        $contribution = (new \ReflectionClass(Contribution::class))->newInstanceWithoutConstructor();
        $contribution->replay($events);

        $this->identityMap[$contributionId] = $contribution;

        return $contribution;
    }

    public function persist(Contribution $contribution): void
    {
        $events = array_map(
            fn(DomainMessage $message) => $message->withAddedMetadata('_aggregate_type', Contribution::class),
            $contribution->popEvents()
        );

        $streamName = $this->streamName($contribution->id());
        $firstEvent = \reset($events);

        if (1 === $firstEvent->metadata()['_aggregate_version']) {
            $stream = new Stream($streamName, new \ArrayIterator($events));
            $this->eventStore->create($stream);
        } else {
            $this->eventStore->appendTo($streamName, new \ArrayIterator($events));
        }

        $contributionId = $contribution->id()->toString();
        if (isset($this->identityMap[$contributionId])) {
            unset($this->identityMap[$contributionId]);
        }
    }

    private function streamName(ContributionId $id): StreamName
    {
        return new StreamName(sprintf('contribution-%s', $id->toString()));
    }
}
