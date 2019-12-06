<?php
declare(strict_types=1);

namespace App\Prooph;

use Prooph\EventStore\EventId;

interface DomainEvent
{
    public function eventId(): ?string;

    public function eventType(): string;

    public function toArray(): array;

    public static function from(EventId $eventId, array $data): self;
}
