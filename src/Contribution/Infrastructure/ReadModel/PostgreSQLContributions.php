<?php
declare(strict_types=1);

namespace App\Contribution\Infrastructure\ReadModel;

use Doctrine\DBAL\Connection;
use App\Contribution\Application\ReadModel\Contributions;

final class PostgreSQLContributions implements Contributions
{
    private const TABLE_NAME = 'contributions';
    private const PAGE_SIZE = 10;

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    //public function __invoke(
    //    EventStorePersistentSubscription $subscription,
    //    ResolvedEvent $resolvedEvent,
    //    ?int $retryCount = null
    //): Promise {
    //    $event = $resolvedEvent->event();
    //    $data = Json::decode($event->data());

    //    switch ($event->eventType()) {
    //        case 'contribution-opened':
    //            $sql = 'INSERT INTO %s (id, title, "projectName", url, state, "createdAt", "updatedAt") VALUES (:id, :title, :projectName, :url, \'opened\', :createdAt, :updatedAt)';
    //            $data['projectName'] = '???/???';
    //            if (1 === preg_match('#^https://github.com/(.*)/p#', $data['url'], $matches)) {
    //                $data['projectName'] = $matches[1];
    //            }
    //            $this->connection->executeUpdate(sprintf($sql, self::TABLE_NAME), $data);
    //            break;

    //        case 'contribution-closed':
    //            $sql = 'UPDATE %s SET state = \'closed\', "closedAt" = :closedAt WHERE id = :id';
    //            $this->connection->executeUpdate(sprintf($sql, self::TABLE_NAME), $data);
    //            break;

    //        case 'contribution-merged':
    //            $sql = 'UPDATE %s SET state = \'merged\' WHERE id = :id';
    //            $this->connection->executeUpdate(sprintf($sql, self::TABLE_NAME), $data);
    //            break;
    //    }

    //    return new Success();
    //}

    public function all(int $page = 1): array
    {
        $offset = ($page - 1) * self::PAGE_SIZE;
        $sql = <<<SQL
    SELECT * FROM %s
        ORDER by
            CASE state
                WHEN 'opened' then 1
                ELSE 2
            END,
            "updatedAt" DESC
SQL;


        return $this->connection->fetchAll(sprintf($sql, self::TABLE_NAME));
    }

    public function lastModified(): \DateTimeImmutable
    {
        $updatedAt =  $this->connection->fetchColumn(sprintf(
            'SELECT "updatedAt" FROM %s ORDER BY "updatedAt" DESC LIMIT 1',
            self::TABLE_NAME
        ));

        if (false === $updatedAt) {
            return new \DateTimeImmutable('now');
        }

        return \DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $updatedAt,
            new \DateTimeZone('UTC')
        );
    }
}
