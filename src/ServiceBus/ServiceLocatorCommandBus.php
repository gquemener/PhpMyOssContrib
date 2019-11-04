<?php
declare(strict_types=1);

namespace App\ServiceBus;

use App\Github\Domain\Command\SynchronizePullRequests;
use App\Github\Domain\Command\SynchronizePullRequestsHandler;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

final class ServiceLocatorCommandBus implements CommandBus, ServiceSubscriberInterface
{
    private $locator;

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    public function dispatch(object $command): void
    {
        $commandClass = get_class($command);

        if (!$this->locator->has($commandClass)) {
            throw new \InvalidArgumentException(\sprintf(
                'No handler found for command "%s"',
                get_class($command)
            ));
        }

        $this->locator->get($commandClass)($command);
    }

    public static function getSubscribedServices()
    {
        return [
            SynchronizePullRequests::class => SynchronizePullRequestsHandler::class,
        ];
    }
}
