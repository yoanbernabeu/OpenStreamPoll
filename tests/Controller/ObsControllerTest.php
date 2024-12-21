<?php

namespace App\Tests\Controller;

use App\Entity\Poll;
use App\Service\Poll\PollService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ObsControllerTest extends WebTestCase
{
    private $client;
    private $pollServiceMock;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->pollServiceMock = $this->createMock(PollService::class);
        self::getContainer()->set('App\Service\Poll\PollService', $this->pollServiceMock);
    }

    public function testIndexWithActivePoll(): void
    {
        $poll = new Poll();
        $this->pollServiceMock
            ->expects($this->once())
            ->method('getActivePoll')
            ->willReturn($poll);

        $this->client->request('GET', '/obs');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testIndexWithNoPoll(): void
    {
        $this->pollServiceMock
            ->expects($this->once())
            ->method('getActivePoll')
            ->willReturn(null);

        $this->client->request('GET', '/obs');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testResultsWithActivePoll(): void
    {
        $poll = new Poll();
        $this->pollServiceMock
            ->expects($this->once())
            ->method('getActivePoll')
            ->willReturn($poll);

        $this->client->request('GET', '/obs/results');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testResultsWithNoPoll(): void
    {
        $this->pollServiceMock
            ->expects($this->once())
            ->method('getActivePoll')
            ->willReturn(null);

        $this->client->request('GET', '/obs/results');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
