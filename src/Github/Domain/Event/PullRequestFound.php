<?php
declare(strict_types=1);

namespace App\Github\Domain\Event;

use App\Github\Domain\Model\PullRequestId;
use App\Github\Domain\Model\PullRequestState;
use DateTimeImmutable;
use DateTimeInterface;

final class PullRequestFound
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
            'number' => (int) $item['number'],
            'title' => (string) $item['title'],
            'url' => (string) $item['html_url'],
            'state' => (string) $item['state'],
            'created_at' => (string) $item['created_at'],
            'updated_at' => (string) $item['updated_at'],
            'closed_at' => $item['closed_at'],
        ]);
    }

    public function aggregateId(): PullRequestId
    {
        return PullRequestId::fromInt($this->payload['aggregate_id']);
    }

    public function number(): int
    {
        return $this->payload['number'];
    }

    public function title(): string
    {
        return $this->payload['title'];
    }

    public function url(): string
    {
        return $this->payload['url'];
    }

    public function state(): PullRequestState
    {
        return PullRequestState::fromName($this->payload['state']);
    }

    public function createdAt(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat(
            DateTimeInterface::ISO8601,
            $this->payload['created_at']
        );
    }

    public function updatedAt(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat(
            DateTimeInterface::ISO8601,
            $this->payload['updated_at']
        );
    }


    public function closedAt(): ?DateTimeImmutable
    {
        return $this->payload['closed_at'] ?
            DateTimeImmutable::createFromFormat(
                DateTimeInterface::ISO8601,
                $this->payload['closed_at']
            ) :
            null;
    }
}
