<?php
declare(strict_types=1);

namespace App\Controller;

use App\Contribution\Domain\Repository\ContributionRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ContributionController implements ServiceSubscriberInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index(Request $request, ContributionRepository $repository)
    {
        return new JsonResponse(
            $repository->all((int) $request->query->get('page', 1))
        );
    }

    public static function getSubscribedServices()
    {
        return [
            'twig' => '?'.Environment::class,
        ];
    }
}
