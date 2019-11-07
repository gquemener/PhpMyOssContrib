<?php
declare(strict_types=1);

namespace App\Github\Domain\Finder;

interface PullRequestFinder
{
    public function all(): object;

    public function merged(): object;
}
