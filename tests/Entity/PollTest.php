<?php

namespace App\Tests\Entity;

use App\Entity\Poll;
use App\Entity\Vote;
use PHPUnit\Framework\TestCase;

class PollTest extends TestCase
{
    private Poll $poll;
    private \DateTimeImmutable $startAt;
    private \DateTimeImmutable $endAt;

    protected function setUp(): void
    {
        $this->poll = new Poll();
        $this->startAt = new \DateTimeImmutable('2024-01-01 10:00:00');
        $this->endAt = new \DateTimeImmutable('2024-01-01 11:00:00');
    }

    public function testPollBasicAttributes(): void
    {
        $this->poll
            ->setTitle('Test Poll')
            ->setShortCode('abc123')
            ->setStartAt($this->startAt)
            ->setEndAt($this->endAt);

        $this->assertEquals('Test Poll', $this->poll->getTitle());
        $this->assertEquals('abc123', $this->poll->getShortCode());
        $this->assertSame($this->startAt, $this->poll->getStartAt());
        $this->assertSame($this->endAt, $this->poll->getEndAt());
    }

    public function testPollQuestions(): void
    {
        $this->poll
            ->setQuestion1('Question 1')
            ->setQuestion2('Question 2')
            ->setQuestion3('Question 3')
            ->setQuestion4('Question 4')
            ->setQuestion5('Question 5');

        $this->assertEquals('Question 1', $this->poll->getQuestion1());
        $this->assertEquals('Question 2', $this->poll->getQuestion2());
        $this->assertEquals('Question 3', $this->poll->getQuestion3());
        $this->assertEquals('Question 4', $this->poll->getQuestion4());
        $this->assertEquals('Question 5', $this->poll->getQuestion5());
    }

    public function testVoteManagement(): void
    {
        $vote = new Vote();
        $this->poll->addVote($vote);
        
        $this->assertCount(1, $this->poll->getVotes());
        $this->assertSame($this->poll, $vote->getPoll());
        
        $this->poll->removeVote($vote);
        $this->assertCount(0, $this->poll->getVotes());
        $this->assertNull($vote->getPoll());
    }

    public function testGetVotesForChoice(): void
    {
        $vote1 = new Vote();
        $vote1->setChoice(1);
        
        $vote2 = new Vote();
        $vote2->setChoice(1);
        
        $vote3 = new Vote();
        $vote3->setChoice(2);

        $this->poll->addVote($vote1);
        $this->poll->addVote($vote2);
        $this->poll->addVote($vote3);

        $this->assertEquals(2, $this->poll->getVotesForChoice(1));
        $this->assertEquals(1, $this->poll->getVotesForChoice(2));
        $this->assertEquals(0, $this->poll->getVotesForChoice(3));
    }

    public function testGetTotalVotes(): void
    {
        $vote1 = new Vote();
        $vote2 = new Vote();

        $this->poll->addVote($vote1);
        $this->poll->addVote($vote2);

        $this->assertEquals(2, $this->poll->getTotalVotes());
    }

    public function testRemoveVote(): void
    {
        $vote = new Vote();
        $this->poll->addVote($vote);
        $this->assertEquals(1, $this->poll->getTotalVotes());

        $this->poll->removeVote($vote);
        $this->assertEquals(0, $this->poll->getTotalVotes());
    }

    public function testNullableFields(): void
    {
        $this->assertNull($this->poll->getQuestion1());
        $this->assertNull($this->poll->getQuestion2());
        $this->assertNull($this->poll->getQuestion3());
        $this->assertNull($this->poll->getQuestion4());
        $this->assertNull($this->poll->getQuestion5());
    }
}
