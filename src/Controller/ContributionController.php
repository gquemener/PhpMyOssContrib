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
        return new JsonResponse(
            $repository->all((int) $request->query->get('page', 1)),
            200,
            [
                'Pages-Count' => $repository->pagesCount(),
                'Opened-Contributions-Count' => $repository->openedCount(),
            ]
        );
    }
}
