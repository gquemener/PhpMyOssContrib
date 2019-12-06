<?php
declare(strict_types=1);

namespace App\Contribution\Infrastructure\Repository;

use App\Contribution\Domain\Model\Contribution;
use App\Contribution\Domain\Model\ContributionId;
use App\Contribution\Domain\Repository\ContributionRepository;
use App\Prooph\AggregateRepository;
use App\Prooph\AggregateRoot;
use App\Prooph\MessageTransformer;
use Prooph\EventStore\Async\EventStoreConnection;
use function Amp\Promise\wait;

final class EventStoreContributionRepository extends AggregateRepository implements ContributionRepository
{
    public function __construct(EventStoreConnection $connection, MessageTransformer $transformer)
    {
        parent::__construct($connection, $transformer, 'contribution', Contribution::class);
    }

    public function find(ContributionId $id): ?Contribution
    {
        return wait($this->getAggregateRoot($id->toString()));
    }

    public function persist(Contribution $contribution): void
    {
        wait($this->saveAggregateRoot($contribution));
    }
}
