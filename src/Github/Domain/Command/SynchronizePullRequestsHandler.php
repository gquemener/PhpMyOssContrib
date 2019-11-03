<?php
declare(strict_types=1);

namespace App\Github\Domain\Command;

use App\Github\Domain\Repository\PullRequestRepository;
use App\ServiceBus\EventBus;

final class SynchronizePullRequestsHandler
{
    private $repository;
    private $eventBus;

    public function __construct(
        PullRequestRepository $repository,
        EventBus $eventBus
    ) {
        $this->repository = $repository;
        $this->eventBus = $eventBus;
    }

    public function __invoke(SynchronizePullRequests $command): void
    {
        foreach ($this->repository->all() as $pullRequest) {
            foreach ($pullRequest->popEvents() as $event) {
                $this->eventBus->dispatch($event);
            }
        }

        foreach ($this->repository->merged() as $pullRequest) {
            foreach ($pullRequest->popEvents() as $event) {
                $this->eventBus->dispatch($event);
            }
        }
    }
}
