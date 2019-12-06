<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Amp\Loop;
use Prooph\EventStore\Async\EventStoreConnection;
use Prooph\EventStore\Async\EventAppearedOnPersistentSubscription;
use Prooph\EventStore\PersistentSubscriptionSettings;
use Prooph\EventStore\UserCredentials;
use Prooph\EventStore\Internal\PersistentSubscriptionCreateResult;
use Prooph\EventStore\Internal\PersistentSubscriptionCreateStatus;
use Prooph\EventStore\Exception\InvalidOperationException;
use App\Contribution\Infrastructure\ReadModel\Registry;
use Symfony\Component\Console\Input\InputArgument;

final class SubscribeToContributionEvents extends Command
{
    protected static $defaultName = 'app:read-model:subscribe';

    private $connection;

    private $registry;

    public function __construct(
        EventStoreConnection $connection,
        Registry $registry
    ) {
        parent::__construct();

        $this->connection = $connection;
        $this->registry = $registry;
    }

    public function configure()
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        Loop::run(function() use ($id) {
            if (null === $connection = $this->registry->find($id)) {
                throw new \InvalidArgumentException(sprintf(
                    'Could not find connection "%s"',
                    $id
                ));
            }

            try {
                $settings = PersistentSubscriptionSettings::create()
                    ->startFromBeginning()
                    ->resolveLinkTos()
                    ->build();

                yield $this->connection
                           ->createPersistentSubscriptionAsync(
                               $connection->stream,
                               $connection->groupName,
                               $settings,
                           );
            } catch (InvalidOperationException $e) {
            }

            yield $this->connection->connectToPersistentSubscriptionAsync(
                $connection->stream,
                $connection->groupName,
                $connection->subscriber
            );
        });
    }
}
