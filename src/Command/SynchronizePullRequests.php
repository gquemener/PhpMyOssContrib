<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Github\Domain\Command\SynchronizePullRequests as SynchronizePullRequestsCommand;
use App\ServiceBus\CommandBus;

final class SynchronizePullRequests extends Command
{
    protected static $defaultName = 'app:github:synchronize';

    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        parent::__construct();

        $this->commandBus = $commandBus;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->commandBus->dispatch(
            new SynchronizePullRequestsCommand()
        );
    }
}
