<?php
declare(strict_types=1);

namespace App\Contribution\Domain\Model;

use App\Contribution\Domain\Event\ContributionClosed;
use App\Contribution\Domain\Event\ContributionMerged;
use App\Contribution\Domain\Event\ContributionOpened;
use App\Prooph\AggregateRoot;
use App\Prooph\DomainEvent;
use InvalidArgumentException;
use Prooph\EventStore\EventId;

final class Contribution extends AggregateRoot
{
    private ContributionState $state;

    public static function open(
        ContributionId $id,
        string $title,
        string $url,
        DateTime $createdAt,
        DateTime $updatedAt
    ): self {
        $self = new self();
        $self->recordThat(ContributionOpened::from(
            EventId::generate(),
            [
                'id' => $id->toString(),
                'title' => $title,
                'url' => $url,
                'createdAt' => $createdAt->toString(),
                'updatedAt' => $updatedAt->toString(),
            ]
        ));

        return $self;
    }

    public function merge(): void
    {
        if ($this->state->equals(ContributionState::closed())) {
            $this->recordThat(ContributionMerged::from(
                EventId::generate(),
                [
                    'id' => $this->id->toString(),
                ]
            ));
        }
    }

    public function close(DateTime $closedAt): void
    {
        if ($this->state->equals(ContributionState::opened())) {
            $this->recordThat(ContributionClosed::from(
                EventId::generate(),
                [
                    'id' => $this->id->toString(),
                    'closedAt' => $closedAt->toString(),
                ]
            ));
        }
    }

    public function aggregateId(): string
    {
        return $this->id->toString();
    }

    protected function apply(DomainEvent $event): void
    {
        switch (get_class($event)) {
            case ContributionOpened::class:
                $this->id = $event->aggregateId();
                $this->state = ContributionState::opened();
                break;

            case ContributionClosed::class:
                $this->state = ContributionState::closed();
                break;

            case ContributionMerged::class:
                $this->state = ContributionState::merged();
                break;

            default:
                throw new InvalidArgumentException(sprintf(
                    'Event "%s" cannot be applied',
                    get_class($event)
                ));
        }
    }
}
