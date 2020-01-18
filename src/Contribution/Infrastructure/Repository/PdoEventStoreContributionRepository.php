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
use Prooph\EventStore\Metadata\MetadataMatcher;
use Prooph\EventStore\Metadata\Operator;

final class PdoEventStoreContributionRepository implements ContributionRepository
{
    public const STREAM_NAME = 'contributions';

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

        $streamName = $this->streamName();
        $metadataMatcher = new MetadataMatcher();
        $metadataMatcher = $metadataMatcher->withMetadataMatch(
            '_aggregate_type',
            Operator::EQUALS(),
            Contribution::class
        );
        $metadataMatcher = $metadataMatcher->withMetadataMatch(
            '_aggregate_id',
            Operator::EQUALS(),
            $contributionId
        );

        try {
            $events = $this->eventStore->load($streamName, 1, null, $metadataMatcher);
        } catch (StreamNotFound $e) {
            $stream = new Stream($streamName, new \ArrayIterator([]));
            $this->eventStore->create($stream);

            return $this->find($id);
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

        $firstEvent = \reset($events);

        $this->eventStore->appendTo($this->streamName(), new \ArrayIterator($events));

        $contributionId = $contribution->id()->toString();
        if (isset($this->identityMap[$contributionId])) {
            unset($this->identityMap[$contributionId]);
        }
    }

    private function streamName(): StreamName
    {
        return new StreamName(self::STREAM_NAME);
    }
}
