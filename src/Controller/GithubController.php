<?php
declare(strict_types=1);

namespace App\Controller;

use App\Github\Domain\Command\SynchronizePullRequests;
use App\ServiceBus\CommandBus;
use Symfony\Component\HttpFoundation\Response;

final class GithubController
{
    public function sync(CommandBus $commandBus)
    {
        $commandBus->dispatch(
            new SynchronizePullRequests()
        );

        return new Response();
    }
}
