<?php
declare(strict_types=1);

namespace App\Contribution\Domain\Model;

use App\Contribution\Domain\Event\ContributionClosed;
use App\Contribution\Domain\Event\ContributionMerged;
use App\Contribution\Domain\Event\ContributionOpened;
use App\Prooph\AggregateRoot;
use InvalidArgumentException;
use App\EventSourcing\AggregateChanged;

final class Contribution
{
    private $id;
    private ContributionState $state;

    private $events = [];
    private $version = 0;

    public static function open(
        ContributionId $id,
        string $title,
        string $url,
        DateTime $createdAt,
        DateTime $updatedAt
    ): self {
        $self = new self();
        $self->recordThat(ContributionOpened::occur(
            $id->toString(),
            [
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
            $this->recordThat(ContributionMerged::occur(
                $this->id->toString()
            ));
        }
    }

    public function close(DateTime $closedAt): void
    {
        if ($this->state->equals(ContributionState::opened())) {
            $this->recordThat(ContributionClosed::occur(
                $this->id->toString(),
                [
                    'closedAt' => $closedAt->toString(),
                ]
            ));
        }
    }

    public function id(): ContributionId
    {
        return $this->id;
    }

    public function replay(\Iterator $history): void
    {
        foreach ($history as $pastEvent) {
            $this->version = $pastEvent->version();

            $this->apply($pastEvent);
        }
    }

    public function apply(AggregateChanged $event): void
    {
        switch (get_class($event)) {
            case ContributionOpened::class:
                $this->id = $event->contributionId();
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

    public function popEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    private function recordThat(AggregateChanged $event): void
    {
        ++$this->version;
        $this->events[] = $event->withVersion($this->version);
        $this->apply($event);
    }

}
