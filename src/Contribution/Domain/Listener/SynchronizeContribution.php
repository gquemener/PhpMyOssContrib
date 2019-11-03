<?php
declare(strict_types=1);

namespace App\Contribution\Domain\Listener;

use App\Github\Domain\Event\PullRequestFound;
use App\Github\Domain\Model\PullRequestState;
use App\Contribution\Domain\Repository\ContributionRepository;
use App\Contribution\Domain\Model\ContributionId;
use App\Contribution\Domain\Model\Contribution;

final class SynchronizeContribution
{
    private $repository;

    public function __construct(ContributionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(PullRequestFound $event): void
    {
        $id = ContributionId::fromGithubId($event->aggregateId());
        if (null === $contribution = $this->repository->find($id)) {
            $contribution = Contribution::open(
                $id,
                $event->title(),
                $event->url(),
                $event->createdAt(),
                $event->updatedAt()
            );
        }

        if ((PullRequestState::closed())->equals($event->state())) {
            $contribution->close($event->closedAt());
        }

        $this->repository->persist($contribution);
    }
}
