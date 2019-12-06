<?php
declare(strict_types=1);

namespace App\Contribution\Domain\Event;

use App\Prooph\DomainEvent;
use Prooph\EventStore\EventId;
use App\Contribution\Domain\Model\ContributionId;

final class ContributionMerged implements DomainEvent
{
    private ?string $eventId;
    private array $data;

    public static function from(EventId $eventId, array $data): self
    {
        $self = new self();
        $self->eventId = $eventId->toString();
        $self->data = $data;

        return $self;
    }

    public function eventId(): ?string
    {
        return $this->eventId;
    }

    public function eventType(): string
    {
        return 'contribution-merged';
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function aggregateId(): ContributionId
    {
        return ContributionId::fromString($this->data['id']);
    }
}
