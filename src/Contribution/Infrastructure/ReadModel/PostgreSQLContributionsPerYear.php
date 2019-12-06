<?php
declare(strict_types=1);

namespace App\Contribution\Infrastructure\ReadModel;

use Amp\Promise;
use Prooph\EventStore\Async\EventAppearedOnPersistentSubscription;
use Prooph\EventStore\Async\EventStorePersistentSubscription;
use Prooph\EventStore\ResolvedEvent;
use Doctrine\DBAL\Connection;
use Amp\Success;
use Prooph\EventStore\Util\Json;
use App\Contribution\Domain\Model\DateTime;

final class PostgreSQLContributionsPerYear implements EventAppearedOnPersistentSubscription
{
    private const TABLE_NAME = 'contributions_per_year';

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function __invoke(
        EventStorePersistentSubscription $subscription,
        ResolvedEvent $resolvedEvent,
        ?int $retryCount = null
    ): Promise {
        $event = $resolvedEvent->event();
        $data = Json::decode($event->data());

        switch ($event->eventType()) {
            case 'contribution-opened':
                $sql = <<<SQL
                    INSERT INTO %s (year, merged, ids) VALUES (:year, 0, ARRAY[:id::bigint])
                        ON CONFLICT ON CONSTRAINT contributions_per_year_year
                            DO UPDATE SET ids = array_append(contributions_per_year.ids, :id::bigint)
SQL;

                $createdAt = DateTime::fromString($data['createdAt']);
                $this->connection->executeUpdate(sprintf($sql, self::TABLE_NAME), [
                    'year' => $createdAt->year(),
                    'id' => (int) $data['id'],
                ]);
                break;

            case 'contribution-merged':
                $sql = 'UPDATE %s SET merged = merged + 1 WHERE :id = ANY (ids)';
                $this->connection->executeUpdate(sprintf($sql, self::TABLE_NAME), ['id' => (int) $data['id']]);
                break;
        }

        return new Success();
    }
}
