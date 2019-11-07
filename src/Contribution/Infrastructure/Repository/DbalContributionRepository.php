<?php
declare(strict_types=1);

namespace App\Contribution\Infrastructure\Repository;

use App\Contribution\Domain\Repository\ContributionRepository;
use App\Contribution\Domain\Model\Contribution;
use App\Contribution\Domain\Model\ContributionId;
use Doctrine\DBAL\Connection;

final class DbalContributionRepository implements ContributionRepository
{
    private const TABLE_NAME = 'contributions';

    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function find(ContributionId $id): ?Contribution
    {
        $sql = sprintf('SELECT * FROM %s WHERE id = :id', self::TABLE_NAME);

        $result = $this->connection->fetchAssoc($sql, ['id' => $id->toInt()]);
        if (false === $result) {
            return null;
        }

        return Contribution::fromArray($result);
    }

    public function persist(Contribution $contribution): void
    {
        $sql = <<<SQL
INSERT INTO %s (id, title, url, state, created_at, updated_at, closed_at)
    VALUES (:id, :title, :url, :state, :created_at, :updated_at, :closed_at)
ON CONFLICT ON CONSTRAINT contributions_pkey
    DO UPDATE SET
        title = :title,
        url = :url,
        state = :state,
        created_at = :created_at,
        updated_at = :updated_at,
        closed_at = :closed_at
SQL;
        $this->connection->executeUpdate(sprintf($sql, self::TABLE_NAME), $contribution->toArray());
    }

    public function all(int $page = 1): array
    {
        $offset = ($page - 1) * 10;
        $sql = sprintf('SELECT * FROM %s ORDER by created_at DESC LIMIT 10 OFFSET %d ', self::TABLE_NAME, $offset);

        $stmt = $this->connection->query($sql);

        return array_map([Contribution::class, 'fromArray'], $stmt->fetchAll());
    }
}
