<?php
declare(strict_types=1);

namespace App\Contribution\Application\ReadModel;

use App\Contribution\Domain\Model\ContributionId;
use App\Contribution\Domain\Model\DateTime;

interface Contributions
{
    public function all(int $page = 1): array;

    public function lastModified(): \DateTimeImmutable;
}
