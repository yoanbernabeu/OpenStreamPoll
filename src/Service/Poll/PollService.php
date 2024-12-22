<?php

declare(strict_types=1);

namespace App\Service\Poll;

use App\Entity\Poll;
use App\Repository\PollRepository;
use Doctrine\ORM\EntityManagerInterface;

class PollService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PollRepository $pollRepository,
    ) {
    }

    public function getActivePoll(): ?Poll
    {
        return $this->pollRepository->findOneActive();
    }

    public function checkIfPollIsActive(): bool
    {
        return null !== $this->pollRepository->findOneActive();
    }

    public function checkIfPollHasVotes(Poll $poll): bool
    {
        return $poll->getVotes()->count() > 0;
    }

    public function checkIfPollIsExpired(Poll $poll): bool
    {
        return $poll->getEndAt() < new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    /**
     * Create a new Poll instance.
     */
    public function createPoll(): Poll
    {
        $poll = new Poll();

        $poll
            ->setStartAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
            ->setEndAt(new \DateTimeImmutable('+2 minutes', new \DateTimeZone('UTC')))
            ->setShortCode($this->generateShortCode())
        ;

        return $poll;
    }

    /**
     * Persist a Poll instance.
     */
    public function persistPoll(Poll $poll): Poll
    {
        $activePoll = $this->getActivePoll();
        if ($activePoll && $activePoll->getId() !== $poll->getId()) {
            $poll->setDraft(true);
        }

        $this->entityManager->persist($poll);
        $this->entityManager->flush();

        return $poll;
    }

    public function removePoll(Poll $poll): void
    {
        $this->entityManager->remove($poll);
        $this->entityManager->flush();
    }

    private function generateShortCode(): string
    {
        $prefix = base_convert(date('Ymd'), 10, 36);
        $suffix = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 4);

        return $prefix.$suffix;
    }
}
