<?php
declare(strict_types=1);

namespace App\Prooph;

use App\Prooph\DomainEvent;
use Prooph\EventStore\ResolvedEvent;
use Prooph\EventStore\Util\Json;
use Prooph\EventStore\EventData;
use Prooph\EventStore\EventId;

final class MessageTransformer
{
    /**
     * key = event type
     * value = event class name
     * @var array
     */
    protected $map;

    // key = event-type, value = event class name
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    public function toDomainEvent(ResolvedEvent $event): DomainEvent
    {
        $event = $event->originalEvent();
        $eventType = $event->eventType();

        if (!isset($this->map[$eventType])) {
            throw new \RuntimeException(
                'No event class for type ' . $eventType . ' given'
            );
        }

        $payload = Json::decode($event->data());

        $class = $this->map[$eventType];

        return $class::from($event->eventId(), $payload);
    }

    public function toEventData(DomainEvent $event): EventData
    {
        if ($eventId = $event->eventId()) {
            $eventId = EventId::fromString($eventId);
        } else {
            $eventId = EventId::generate();
        }

        return new EventData(
            $eventId,
            $event->eventType(),
            true,
            Json::encode($event->toArray())
        );
    }
}
