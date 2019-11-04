<?php
declare(strict_types=1);

namespace App\Controller;

use App\Contribution\Domain\Repository\ContributionRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;

final class DefaultController implements ServiceSubscriberInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index()
    {
        return new Response(
            $this->container->get('twig')->render('contributions/index.html.twig'
        ));
    }

    public static function getSubscribedServices()
    {
        return [
            'twig' => '?'.Environment::class,
        ];
    }
}
