<?php
declare(strict_types=1);

namespace App\Contribution\Infrastructure\ReadModel;

use Prooph\EventStore\Async\EventAppearedOnPersistentSubscription;

final class PersistentSubscriptionConnection
{
    public string $id;
    public string $stream;
    public string $groupName;
    public EventAppearedOnPersistentSubscription $subscriber;

    private function __construct(
        string $id,
        string $stream,
        string $groupName,
        EventAppearedOnPersistentSubscription $subscriber
    ) {
        $this->id = $id;
        $this->stream = $stream;
        $this->groupName = $groupName;
        $this->subscriber = $subscriber;
    }

    public static function create(
        string $id,
        string $stream,
        string $groupName,
        EventAppearedOnPersistentSubscription $subscriber
    ): self {
        return new self($id, $stream, $groupName, $subscriber);
    }
}
