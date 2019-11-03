<?php
declare(strict_types=1);

namespace App\Github\Domain\Model;

use App\Github\Domain\Event\PullRequestFound;
use App\Github\Domain\Event\MergedPullRequestFound;

final class PullRequest
{
    private $events = [];

    private function __construct()
    {
    }

    public static function found(array $item): self
    {
        $self = new self();
        $self->events[] = PullRequestFound::fromGuzzleApi($item);

        return $self;
    }


    public static function foundMerged(array $item): self
    {
        $self = new self();
        $self->events[] = MergedPullRequestFound::fromGuzzleApi($item);

        return $self;
    }
    public function popEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
