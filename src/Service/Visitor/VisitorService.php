<?php

declare(strict_types=1);

namespace App\Service\Visitor;

use App\Entity\Poll;
use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class VisitorService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function saveVote(Vote $vote): void
    {
        $this->entityManager->persist($vote);
        $this->entityManager->flush();
    }

    public function createVote(Poll $poll, string $voterId): Vote
    {
        $vote = new Vote();
        $vote->setVoterId($voterId);
        $vote->setPoll($poll);
        $vote->setCreatedAt(new \DateTimeImmutable());

        return $vote;
    }

    public function checkIfVisitorHasVoted(string $voterId, Poll $poll): bool
    {
        return $poll->getVotes()->exists(function ($key, $vote) use ($voterId) {
            return $vote->getVoterId() === $voterId;
        });
    }

    public function getClientIdFromRequest(Request $request): string
    {
        $ip = $request->getClientIp();
        $ip = $ip ?: random_bytes(3);
        $ipEncoded = base_convert($ip, 10, 36);

        $userAgent = $request->headers->get('User-Agent');
        $userAgent = $userAgent ?: 'unknown';
        $userAgentEncoded = base_convert($userAgent, 10, 36);

        return $ipEncoded.$userAgentEncoded;
    }
}
