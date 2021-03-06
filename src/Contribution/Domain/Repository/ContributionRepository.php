<?php
declare(strict_types=1);

namespace App\Contribution\Domain\Repository;

use App\Contribution\Domain\Model\ContributionId;
use App\Contribution\Domain\Model\Contribution;

interface ContributionRepository
{
    public function find(ContributionId $id): ?Contribution;

    public function persist(Contribution $contribution): void;
}
