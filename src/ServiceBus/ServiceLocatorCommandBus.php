<?php
declare(strict_types=1);

namespace App\ServiceBus;

use Symfony\Component\DependencyInjection\ServiceLocator;

final class ServiceLocatorCommandBus implements CommandBus
{
    private $locator;

    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    public function dispatch(object $command): void
    {
        $locator = $this->locator;

        if (null === $handler = $locator(get_class($command))) {
            throw new \InvalidArgumentException(\sprintf(
                'No handler found for command "%s"',
                get_class($command)
            ));
        }

        $handler($command);
    }
}
