<?php
declare(strict_types=1);

namespace App\Controller;

use App\Contribution\Domain\Repository\ContributionRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Environment;

final class ContributionController implements ServiceSubscriberInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index(ContributionRepository $repository)
    {
        return new Response(
            $this->container->get('twig')->render('contributions/index.html.twig', [
                'contributions' => $repository->last10(),
            ]));
    }

    public static function getSubscribedServices()
    {
        return [
            'twig' => '?'.Environment::class,
        ];
    }
}
