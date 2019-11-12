<?php
declare(strict_types=1);

namespace App\Contribution\Infrastructure\Repository;

use App\Contribution\Domain\Repository\ContributionRepository;
use App\Contribution\Domain\Model\Contribution;
use App\Contribution\Domain\Model\ContributionId;
use Doctrine\DBAL\Connection;
use App\Contribution\Domain\Model\ContributionState;

final class DbalContributionRepository implements ContributionRepository
{
    private const TABLE_NAME = 'contributions';
    private const PAGE_SIZE = 10;

    private $connection;
    private $identityMap = [];

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function find(ContributionId $id): ?Contribution
    {
        if (null !== $contribution = $this->getFromIdentityMap($id)) {
            return $contribution;
        }
        $sql = sprintf('SELECT * FROM %s WHERE id = :id', self::TABLE_NAME);

        $result = $this->connection->fetchAssoc($sql, ['id' => $id->toInt()]);
        if (false === $result) {
            return null;
        }

        $contribution = Contribution::fromArray($result);
        $this->addToIdentityMap($contribution);

        return $contribution;
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
        $this->addToIdentityMap($contribution);
    }

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
            updated_at DESC
SQL;


        $contributions = array_map(
            [Contribution::class, 'fromArray'],
            $this->connection->fetchAll(sprintf($sql, self::TABLE_NAME))
        );

        foreach ($contributions as $contribution) {
            $this->addToIdentityMap($contribution);
        }

        return $contributions;
    }

    public function lastModified(): \DateTimeImmutable
    {
        $updatedAt =  $this->connection->fetchColumn(sprintf(
            'SELECT updated_at FROM %s ORDER BY updated_at DESC LIMIT 1',
            self::TABLE_NAME
        ));

        return \DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $updatedAt,
            new \DateTimeZone('UTC')
        );
    }

    private function getFromIdentityMap(ContributionId $id): ?Contribution
    {
        return $this->identityMap[$id->toInt()] ?? null;
    }

    private function addToIdentityMap(Contribution $contribution): void
    {
        $this->identityMap[$contribution->id()->toInt()] = $contribution;
    }
}
