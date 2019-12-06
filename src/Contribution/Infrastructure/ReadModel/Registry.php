<?php
declare(strict_types=1);

namespace App\Contribution\Infrastructure\ReadModel;

use Assert\Assertion;

final class Registry
{
    private $connections;

    public function __construct(array $connections)
    {
        Assertion::allIsInstanceOf($connections, PersistentSubscriptionConnection::class);
        $this->connections = $connections;
    }

    public function find(string $id): ?PersistentSubscriptionConnection
    {
        foreach ($this->connections as $connection) {
            if ($connection->id === $id) {
                return $connection;
            }
        }

        return null;
    }
}
