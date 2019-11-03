<?php
declare(strict_types=1);

namespace App\Contribution\Domain\Listener;

use App\Github\Domain\Event\MergedPullRequestFound;
use App\Contribution\Domain\Repository\ContributionRepository;
use App\Contribution\Domain\Model\ContributionId;

final class MarkMergedContribution
{
    private $repository;

    public function __construct(ContributionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(MergedPullRequestFound $event): void
    {
        $id = ContributionId::fromGithubId($event->aggregateId());
        if (null === $contribution = $this->repository->find($id)) {
            throw new \InvalidArgumentException(sprintf('Could not find contribution #%d', $id->toInt()));
        }

        $contribution->merge();

        $this->repository->persist($contribution);
    }
}
