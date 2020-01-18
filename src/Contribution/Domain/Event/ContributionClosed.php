<?php
declare(strict_types=1);

namespace App\Contribution\Domain\Event;

use App\EventSourcing\AggregateChanged;
use App\Contribution\Domain\Model\DateTime;
use App\Contribution\Domain\Model\ContributionId;

final class ContributionClosed extends AggregateChanged
{
    public function contributionId(): ContributionId
    {
        return ContributionId::fromString(
            $this->aggregateId()
        );
    }

    public function closedAt(): DateTime
    {
        return DateTime::fromString($this->payload()['closedAt']);
    }
}
