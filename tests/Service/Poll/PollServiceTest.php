<?php

namespace App\Tests\Service\Poll;

use App\Entity\Poll;
use App\Repository\PollRepository;
use App\Service\Poll\PollService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PollServiceTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    /** @var PollRepository&MockObject */
    private PollRepository $pollRepository;

    private PollService $pollService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->pollRepository = $this->createMock(PollRepository::class);
        $this->pollService = new PollService($this->entityManager, $this->pollRepository);
    }

    public function testCreatePoll(): void
    {
        $poll = $this->pollService->createPoll();

        $this->assertInstanceOf(Poll::class, $poll);
        $this->assertNotNull($poll->getStartAt());
        $this->assertNotNull($poll->getEndAt());
        $this->assertNotNull($poll->getShortCode());
    }

    public function testCheckIfPollIsActive(): void
    {
        $this->pollRepository
            ->expects($this->once())
            ->method('findOneActive')
            ->willReturn(new Poll());

        $this->assertTrue($this->pollService->checkIfPollIsActive());
    }

    public function testCheckIfPollHasVotes(): void
    {
        $poll = new Poll();
        $this->assertFalse($this->pollService->checkIfPollHasVotes($poll));
    }

    public function testCheckIfPollIsExpired(): void
    {
        $poll = new Poll();
        $poll->setEndAt(new \DateTimeImmutable('-1 hour'));
        
        $this->assertTrue($this->pollService->checkIfPollIsExpired($poll));
    }
}
