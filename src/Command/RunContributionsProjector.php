<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use App\ServiceBus\CommandBus;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Contribution\Infrastructure\Projector\Contributions;

final class RunContributionsProjector extends Command
{
    protected static $defaultName = 'app:run-projector:contributions';

    private $contributions;

    public function __construct(Contributions $contributions)
    {
        parent::__construct();

        $this->contributions = $contributions;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->contributions->run();
    }
}
