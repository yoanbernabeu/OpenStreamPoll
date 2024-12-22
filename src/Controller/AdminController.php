<?php

namespace App\Controller;

use App\Entity\Poll;
use App\Enum\FlashTypeEnum;
use App\Form\PollType;
use App\Repository\PollRepository;
use App\Service\Poll\PollService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'app_admin_')]
class AdminController extends AbstractController
{
    public function __construct(
        private readonly PollRepository $pollRepository,
        private readonly PollService $pollService,
    ) {
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        $polls = $this->pollRepository->findLatest(10);

        return $this->render('admin/index.html.twig', [
            'polls' => $polls,
        ]);
    }

    #[Route('/show/{id}', name: 'show')]
    public function show(?Poll $poll = null): Response
    {
        if (null === $poll) {
            $this->addFlash(FlashTypeEnum::ERROR->value, 'This poll does not exist.');

            return $this->redirectToRoute('app_admin_index');
        }

        return $this->render('admin/show.html.twig', [
            'poll' => $poll,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {
        $poll = $this->pollService->createPoll();

        if ($this->pollService->checkIfPollIsActive()) {
            $this->addFlash(FlashTypeEnum::ERROR->value, 'Active poll exists. Status set to draft.');
            $poll->setDraft(true);
        }

        return $this->handleForm($request, $poll, 'admin/create.html.twig');
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, ?Poll $poll = null): Response
    {
        if (null === $poll) {
            $this->addFlash(FlashTypeEnum::ERROR->value, 'This poll does not exist.');

            return $this->redirectToRoute('app_admin_index');
        }

        if ($this->pollService->checkIfPollHasVotes($poll)) {
            $this->addFlash(FlashTypeEnum::ERROR->value, 'This poll has votes and cannot be edited.');

            return $this->redirectToRoute('app_admin_show', ['id' => $poll->getId()]);
        }

        return $this->handleForm($request, $poll, 'admin/create.html.twig');
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Poll $poll): Response
    {
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete'.$poll->getId(), (string) $token)) {
            $this->pollService->removePoll($poll);
        }

        return $this->redirectToRoute('app_admin_index');
    }

    #[Route('/_results/{id}', name: 'results')]
    public function results(Poll $poll): Response
    {
        return $this->render('admin/_results.html.twig', [
            'poll' => $poll,
        ]);
    }

    private function handleForm(Request $request, Poll $poll, string $template): Response
    {
        $form = $this->createForm(PollType::class, $poll);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->pollService->persistPoll($poll);

            return $this->redirectToRoute('app_admin_show', ['id' => $poll->getId()]);
        }

        return $this->render($template, [
            'form' => $form,
        ]);
    }
}
