<?php
declare(strict_types=1);

namespace App\Github\Domain\Query;

use GuzzleHttp\Promise\Promise;
use App\Github\Domain\Finder\PullRequestFinder;

final class GetPullRequestsHandler
{
    private $finder;

    public function __construct(PullRequestFinder $finder)
    {
        $this->finder = $finder;
    }

    public function __invoke(): object
    {
        $promise = new Promise();
        $promise->resolve($this->finder->all());

        return $promise;
    }
}
