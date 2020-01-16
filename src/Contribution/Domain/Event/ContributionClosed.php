<?php
declare(strict_types=1);

namespace App\Contribution\Domain\Event;

use App\EventSourcing\AggregateChanged;

final class ContributionClosed extends AggregateChanged
{
}
