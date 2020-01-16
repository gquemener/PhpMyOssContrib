<?php
declare(strict_types=1);

namespace App\Contribution\Domain\Event;

use App\Contribution\Domain\Model\ContributionId;
use App\Contribution\Domain\Model\DateTime;
use App\EventSourcing\AggregateChanged;

final class ContributionOpened extends AggregateChanged
{
    public function contributionId(): ContributionId
    {
        return ContributionId::fromString(
            $this->aggregateId()
        );
    }
}
