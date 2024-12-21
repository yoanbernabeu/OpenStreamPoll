<?php

namespace App\Tests\Entity;

use App\Entity\Poll;
use App\Entity\Vote;
use PHPUnit\Framework\TestCase;

class VoteTest extends TestCase
{
    private Vote $vote;

    protected function setUp(): void
    {
        $this->vote = new Vote();
    }

    public function testVoteAttributes(): void
    {
        $poll = new Poll();
        $createdAt = new \DateTimeImmutable();
        
        $this->vote->setPoll($poll);
        $this->vote->setVoterId('visitor123');
        $this->vote->setCreatedAt($createdAt);
        $this->vote->setChoice(1);

        $this->assertSame($poll, $this->vote->getPoll());
        $this->assertEquals('visitor123', $this->vote->getVoterId());
        $this->assertSame($createdAt, $this->vote->getCreatedAt());
        $this->assertEquals(1, $this->vote->getChoice());
    }
}
