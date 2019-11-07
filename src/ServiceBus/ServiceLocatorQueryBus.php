<?php
declare(strict_types=1);

namespace App\ServiceBus;

use App\ServiceBus\QueryBus;
use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use App\Github\Domain\Query;
use App\Github\Domain\Query\GetPullRequestsHandler;
use Psr\Container\ContainerInterface;

final class ServiceLocatorQueryBus implements QueryBus, ServiceSubscriberInterface
{
    private $locator;

    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    public function dispatch(object $query): object
    {
        $queryClass = get_class($query);

        if (!$this->locator->has($queryClass)) {
            throw new \InvalidArgumentException(\sprintf(
                'No handler found for query "%s"',
                get_class($query)
            ));
        }

        $handler = $this->locator->get($queryClass);
        $promise = $handler($query);
        if (!$promise instanceof PromiseInterface) {
            throw new \RuntimeException(sprintf(
                'Query handler "%s" did not return an instance of "GuzzleHttp\Promise\PromiseInterface", got "%s"',
                get_class($handler),
                get_class($promise)
            ));
        }

        return $promise;
    }

    public static function getSubscribedServices()
    {
        return [
            Query\GetPullRequests::class => '?'.Query\GetPullRequestsHandler::class,
            Query\GetMergedPullRequests::class => '?'.Query\GetMergedPullRequestsHandler::class,
        ];
    }
}
