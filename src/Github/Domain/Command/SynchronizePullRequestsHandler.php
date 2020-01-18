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
use App\Contribution\Domain\Model\DateTime;

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

    public function __invoke(): void
    {
        $promise = $this->queryBus->dispatch(new GetPullRequests());
        foreach ($promise->wait() as $pullRequest) {
            $id = ContributionId::fromString((string) $pullRequest['id']);
            if (null === $contribution = $this->repository->find($id)) {
                $contribution = Contribution::open(
                    $id,
                    (string) $pullRequest['title'],
                    (string) $pullRequest['html_url'],
                    DateTime::fromString($pullRequest['created_at']),
                    DateTime::fromString($pullRequest['updated_at'])
                );
            }

            if ('closed' === (string) $pullRequest['state']) {
                $contribution->close(
                    DateTime::fromString($pullRequest['closed_at'])
                );
            }

            $this->repository->persist($contribution);
        }

        $promise = $this->queryBus->dispatch(new GetMergedPullRequests());
        foreach ($promise->wait() as $pullRequest) {
            $id = ContributionId::fromString((string) $pullRequest['id']);
            if (null === $contribution = $this->repository->find($id)) {
                throw new \InvalidArgumentException(sprintf('Could not find contribution "%s"', $id->toString()));
            }
            $contribution->merge();
            $this->repository->persist($contribution);
        }
    }
}
