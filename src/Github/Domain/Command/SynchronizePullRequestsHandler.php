<?php
declare(strict_types=1);

namespace App\Github\Domain\Command;

use App\ServiceBus\QueryBus;
use App\ServiceBus\EventBus;
use App\Github\Domain\Query\GetPullRequests;
use App\Contribution\Domain\Repository\ContributionRepository;
use App\Contribution\Domain\Model\Contribution;
use App\Github\Domain\Query\GetMergedPullRequests;
use App\Contribution\Domain\Model\ContributionId;

final class SynchronizePullRequestsHandler
{
    private $repository;
    private $queryBus;
    private $eventBus;

    public function __construct(
        ContributionRepository $repository,
        QueryBus $queryBus
    ) {
        $this->repository = $repository;
        $this->queryBus = $queryBus;
    }

    public function __invoke(SynchronizePullRequests $command): void
    {
        $promise = $this->queryBus->dispatch(new GetPullRequests());
        foreach ($promise->wait() as $pullRequest) {
            $this->repository->persist(
                Contribution::fromGithubPullRequest($pullRequest)
            );
        }

        $promise = $this->queryBus->dispatch(new GetMergedPullRequests());
        foreach ($promise->wait() as $pullRequest) {
            $id = ContributionId::fromGithubPullRequest($pullRequest);
            if (null === $contribution = $this->repository->find($id)) {
                throw new \InvalidArgumentException(sprintf('Could not find contribution #%d', $id->toInt()));
            }
            $contribution->merge();
            $this->repository->persist($contribution);
        }
    }
}
