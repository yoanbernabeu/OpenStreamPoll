<?php

namespace App\Tests\Service\Visitor;

use App\Entity\Poll;
use App\Entity\Vote;
use App\Service\Visitor\VisitorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class VisitorServiceTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;
    private VisitorService $visitorService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->visitorService = new VisitorService($this->entityManager);
    }

    public function testCreateVote(): void
    {
        $poll = new Poll();
        $voterId = 'test123';

        $vote = $this->visitorService->createVote($poll, $voterId);

        $this->assertInstanceOf(Vote::class, $vote);
        $this->assertEquals($voterId, $vote->getVoterId());
        $this->assertSame($poll, $vote->getPoll());
        $this->assertNotNull($vote->getCreatedAt());
    }

    public function testSaveVote(): void
    {
        $vote = new Vote();

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($vote);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->visitorService->saveVote($vote);
    }

    public function testCheckIfVisitorHasVoted(): void
    {
        $poll = new Poll();
        $voterId = 'visitor123';

        $vote = new Vote();
        $vote->setVoterId($voterId);
        $poll->addVote($vote);

        $this->assertTrue($this->visitorService->checkIfVisitorHasVoted($voterId, $poll));
        $this->assertFalse($this->visitorService->checkIfVisitorHasVoted('other_visitor', $poll));
    }

    public function testGetClientIdFromRequest(): void
    {
        $request = new Request();
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        $request->headers->set('User-Agent', 'PHPUnit Test Browser');

        $clientId = $this->visitorService->getClientIdFromRequest($request);

        $this->assertIsString($clientId);
        $this->assertNotEmpty($clientId);
    }
}
