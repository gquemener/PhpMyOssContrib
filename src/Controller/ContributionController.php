<?php
declare(strict_types=1);

namespace App\Controller;

use App\Contribution\Domain\Repository\ContributionRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ContributionController
{
    public function index(Request $request, ContributionRepository $repository)
    {
        $response = new JsonResponse();
        $response->setCache([
            'last_modified' => $repository->lastModified(),
            'public' => true,
        ]);

        if ($response->isNotModified($request)) {
            return $response;
        }

        $response->setData($repository->all());

        return $response;
    }
}
