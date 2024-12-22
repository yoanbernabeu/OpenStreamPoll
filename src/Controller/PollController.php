<?php

namespace App\Controller;

use App\Entity\Poll;
use App\Form\VoteType;
use App\Service\Poll\PollService;
use App\Service\Visitor\VisitorService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/poll', name: 'app_poll_')]
class PollController extends AbstractController
{
    public function __construct(
        private readonly VisitorService $visitorService,
        private readonly PollService $pollService,
    ) {
    }

    #[Route('/{shortCode}', name: 'show')]
    public function show(
        Request $request,
        #[MapEntity(mapping: ['shortCode' => 'shortCode'])]
        ?Poll $poll = null,
    ): Response {
        // If the poll does not exist, display an error message
        if (null === $poll) {
            return $this->render('poll/error.html.twig', [
                'message' => 'This poll does not exist.',
            ]);
        }

        // If poll is draft, display an error message
        if ($poll->isDraft()) {
            return $this->render('poll/error.html.twig', [
                'message' => 'This poll is not available.',
            ]);
        }

        // If the poll is expired, display an error message
        if ($this->pollService->checkIfPollIsExpired($poll)) {
            return $this->render('poll/error.html.twig', [
                'message' => 'This poll is no longer available.',
            ]);
        }

        $voterId = $this->visitorService->getClientIdFromRequest($request);

        // Check If Visitor has already voted
        if ($this->visitorService->checkIfVisitorHasVoted($voterId, $poll)) {
            return $this->render('poll/success.html.twig', [
                'title' => $poll->getTitle(),
            ]);
        }

        $vote = $this->visitorService->createVote($poll, $voterId);

        $form = $this->createForm(VoteType::class, $vote);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->visitorService->saveVote($vote);

            return $this->redirectToRoute('app_poll_show', ['shortCode' => $poll->getShortCode()]);
        }

        return $this->render('poll/show.html.twig', [
            'poll' => $poll,
            'voterId' => $voterId,
            'form' => $form,
        ]);
    }
}
