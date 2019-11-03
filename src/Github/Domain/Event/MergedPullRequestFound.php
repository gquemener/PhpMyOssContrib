<?php
declare(strict_types=1);

namespace App\Github\Domain\Event;

use App\Github\Domain\Model\PullRequestId;
use App\Github\Domain\Model\PullRequestState;
use DateTimeImmutable;
use DateTimeInterface;

final class MergedPullRequestFound
{
    private $payload;

    private function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public static function fromGuzzleApi(array $item): self
    {
        return new self([
            'aggregate_id' => (int) $item['id'],
        ]);
    }

    public function aggregateId(): PullRequestId
    {
        return PullRequestId::fromInt($this->payload['aggregate_id']);
    }
}
