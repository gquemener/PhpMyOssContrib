<?php
declare(strict_types=1);

namespace App\ServiceBus;

use Symfony\Component\EventDispatcher\EventDispatcher;

final class SymfonyDispatcherEventBus implements EventBus
{
    private $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatch(object $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}
