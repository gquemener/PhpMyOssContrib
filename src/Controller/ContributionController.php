<?php
declare(strict_types=1);

namespace App\Controller;

use App\Contribution\Domain\Repository\ContributionRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Contribution\Application\ReadModel\Contributions;

final class ContributionController
{
    public function index(Request $request, Contributions $contributions)
    {
        $response = new JsonResponse();
        $response->setCache([
            'last_modified' => $contributions->lastModified(),
            'public' => true,
        ]);

        if ($response->isNotModified($request)) {
            return $response;
        }

        $response->setData($contributions->all());

        return $response;
    }
}
