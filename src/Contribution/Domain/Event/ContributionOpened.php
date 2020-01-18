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

    public function url(): string
    {
        return $this->payload()['url'];
    }

    public function title(): string
    {
        return $this->payload()['title'];
    }

    public function openedAt(): DateTime
    {
        return DateTime::fromString($this->payload()['createdAt']);
    }

    public function modifiedAt(): DateTime
    {
        return DateTime::fromString($this->payload()['updatedAt']);
    }
}
