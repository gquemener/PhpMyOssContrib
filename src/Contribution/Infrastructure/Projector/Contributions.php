<?php
declare(strict_types=1);

namespace App\Contribution\Infrastructure\Projector;

use Prooph\EventStore\StreamName;
use App\Contribution\Infrastructure\Repository\PdoEventStoreContributionRepository;
use App\Contribution\Domain\Event\ContributionOpened;
use App\Contribution\Domain\Event\ContributionClosed;
use App\Contribution\Domain\Event\ContributionMerged;
use Doctrine\DBAL\Connection;
use Prooph\EventStore\Pdo\PdoEventStore;
use Prooph\EventStore\Pdo\Util\PostgresHelper;
use App\EventSourcing\AggregateChanged;

final class Contributions
{
    use PostgresHelper;

    private const PROJECTIONS_TABLE = 'projections';
    private const PROJECTION_NAME = 'contributions';

    private $eventStore;
    private $connection;

    public function __construct(
        PdoEventStore $eventStore,
        Connection $connection
    ) {
        $this->eventStore = $eventStore;
        $this->connection = $connection;
    }

    public function run(): void
    {
        $lastPosition = $this->fetchLastPosition();

        $events = $this->eventStore->load(
            new StreamName(PdoEventStoreContributionRepository::STREAM_NAME),
            $lastPosition + 1
        );

        foreach ($events as $event) {
            $this->connection->transactional(function(Connection $connection) use ($event) {
                xdebug_break();
                $this->project($event);
                $this->persistLastPosition($event->metadata()['_position']);
            });
        }
    }

    private function fetchLastPosition(): int
    {
        $sql = sprintf('SELECT position FROM %s WHERE name = \'%s\'', self::PROJECTIONS_TABLE, self::PROJECTION_NAME);

        if (false === $result = $this->connection->fetchColumn($sql)) {
            return 0;
        }

        return $result;
    }

    private function persistLastPosition(int $lastPosition): void
    {
        $sql = <<<EOT
            INSERT INTO {$this->quoteIdent(self::PROJECTIONS_TABLE)}
                (name, position)
                VALUES ('%s', ?)
            ON CONFLICT ON CONSTRAINT projections_name_key
                DO UPDATE SET position = ?
        EOT;
        $sql = sprintf($sql, self::PROJECTION_NAME);

        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $lastPosition,
            $lastPosition,
        ]);
    }

    private function project(AggregateChanged $event): void
    {
        switch (get_class($event)) {
            case ContributionOpened::class:
                $sql = <<<EOT
                    INSERT INTO {$this->quoteIdent(self::PROJECTION_NAME)}
                        (id, title, "projectName", url, state, "createdAt", "updatedAt")
                        VALUES (?, ?, ?, ?, 'opened', ?, ?)
                EOT;
                $statement = $this->connection->prepare($sql);

                $projectName = '???/???';
                $url = $event->url();
                if (1 === preg_match('#^https://github.com/(.*)/p#', $url, $matches)) {
                    $projectName = $matches[1];
                }
                $statement->execute([
                    $event->contributionId()->toString(),
                    $event->title(),
                    $projectName,
                    $url,
                    $event->openedAt()->toString(),
                    $event->modifiedAt()->toString()
                ]);
                break;

            case ContributionClosed::class:
                $sql = "UPDATE {$this->quoteIdent(self::PROJECTION_NAME)} SET \"closedAt\" = ?, \"state\" = 'closed' WHERE id = ?";
                $statement = $this->connection->prepare($sql);

                $statement->execute([
                    $event->closedAt()->toString(),
                    $event->contributionId()->toString()
                ]);
                break;

            case ContributionMerged::class:
                $sql = "UPDATE {$this->quoteIdent(self::PROJECTION_NAME)} SET \"state\" = 'merged' WHERE id = ?";
                $statement = $this->connection->prepare($sql);

                $statement->execute([
                    $event->contributionId()->toString()
                ]);
                break;
        }
    }
}
