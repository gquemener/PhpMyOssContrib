<?php
declare(strict_types=1);

namespace App\Prooph;

use Prooph\EventStore\ExpectedVersion;

abstract class AggregateRoot
{
    /** @var int */
    protected $expectedVersion = ExpectedVersion::EMPTY_STREAM;

    /**
     * List of events that are not committed to the EventStore
     *
     * @var DomainEvent[]
     */
    protected $recordedEvents = [];

    /**
     * We do not allow public access to __construct
     * this way we make sure that an aggregate root can only
     * be constructed by static factories
     */
    protected function __construct()
    {
    }

    public function expectedVersion(): int
    {
        return $this->expectedVersion;
    }

    public function setExpectedVersion(int $version): void
    {
        $this->expectedVersion = $version;
    }

    /**
     * Get pending events and reset stack
     *
     * @return DomainEvent[]
     */
    public function popRecordedEvents(): array
    {
        $pendingEvents = $this->recordedEvents;

        $this->recordedEvents = [];

        return $pendingEvents;
    }

    /**
     * Record an aggregate changed event
     */
    protected function recordThat(DomainEvent $event): void
    {
        $this->recordedEvents[] = $event;

        $this->apply($event);
    }

    public static function reconstituteFromHistory(array $historyEvents): self
    {
        $instance = new static();
        $instance->replay($historyEvents);

        return $instance;
    }

    /**
     * Replay past events
     *
     * @param DomainEvent[] $historyEvents
     */
    public function replay(array $historyEvents): void
    {
        foreach ($historyEvents as $pastEvent) {
            /** @var DomainEvent $pastEvent */
            $this->apply($pastEvent);
        }
    }

    abstract public function aggregateId(): string;

    /**
     * Apply given event
     */
    abstract protected function apply(DomainEvent $event): void;
}
