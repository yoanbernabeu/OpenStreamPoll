<?php

namespace App\Controller;

use App\Service\Poll\PollService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ObsController extends AbstractController
{
    public function __construct(
        private readonly PollService $pollService,
    ) {
    }

    #[Route('/obs', name: 'app_obs')]
    public function index(): Response
    {
        $poll = $this->pollService->getActivePoll();

        return $this->render('obs/index.html.twig', [
            'poll' => $poll,
        ]);
    }

    #[Route('/obs/results', name: 'app_obs_results')]
    public function results(): Response
    {
        $poll = $this->pollService->getActivePoll();

        return $this->render('obs/_results.html.twig', [
            'poll' => $poll,
        ]);
    }
}
