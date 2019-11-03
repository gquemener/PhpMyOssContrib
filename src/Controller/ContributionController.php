<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Contribution\Domain\Repository\ContributionRepository;
use Symfony\Component\HttpFoundation\Response;

final class ContributionController extends Controller
{
    public function index(ContributionRepository $repository)
    {
        return new Response(
            $this->renderView('contributions/index.html.twig', [
                'contributions' => $repository->last10(),
            ]));
    }
}
