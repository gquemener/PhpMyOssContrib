<?php
declare(strict_types=1);

namespace App\Github\Domain\Repository;

use Traversable;

interface PullRequestRepository
{
    public function all(): Traversable;

    public function merged(): Traversable;
}
