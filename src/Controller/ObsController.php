<?php

namespace App\Controller;

use App\Service\Poll\PollService;
use App\Service\Qr\QrService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ObsController extends AbstractController
{
    public function __construct(
        private readonly PollService $pollService,
        private readonly QrService $qrService,
    ) {
    }

    #[Route('/obs', name: 'app_obs')]
    public function index(): Response
    {
        return $this->render('obs/index.html.twig');
    }

    #[Route('/obs/qr', name: 'app_obs_qr')]
    public function qr(): Response
    {
        return $this->render('obs/qr.html.twig');
    }

    #[Route('/obs/results', name: 'app_obs_results')]
    public function results(): Response
    {
        $poll = $this->pollService->getActivePoll();

        return $this->render('obs/_results.html.twig', [
            'poll' => $poll,
        ]);
    }

    #[Route('/obs/qr/results', name: 'app_obs_qr_results')]
    public function qrResults(): Response
    {
        $poll = $this->pollService->getActivePoll();

        if (null === $poll) {
            return new Response();
        }

        $url = $this->generateUrl(
            'app_poll_show',
            ['shortCode' => $poll->getShortCode()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $qrCode = $this->qrService->generateQrCode($url);

        return $this->render('obs/_qr_results.html.twig', [
            'qrCode' => $qrCode,
        ]);
    }
}
