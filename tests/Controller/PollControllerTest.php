<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Entity\Poll;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PollControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $testPoll;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->resetDatabase();
        $this->loadFixtures();
        $this->createTestPoll();
    }

    private function createTestPoll(): void
    {
        $poll = new Poll();
        $poll->setTitle('Test Poll');
        $poll->setShortCode('test-poll');
        $poll->setStartAt(new \DateTimeImmutable('now'));
        $poll->setEndAt(new \DateTimeImmutable('+1 day'));
        $poll->setQuestion1('Question 1');
        
        $this->entityManager->persist($poll);
        $this->entityManager->flush();
        
        $this->testPoll = $poll;
    }

    private function getCsrfToken(string $tokenId): string
    {
        $this->client->request('GET', '/poll/' . $this->testPoll->getShortCode());
        return $this->client->getContainer()->get('security.csrf.token_manager')->getToken($tokenId)->getValue();
    }

    public function testShowPollWithInvalidShortCode(): void
    {
        $this->client->request('GET', '/poll/invalid-code');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testShowExpiredPoll(): void
    {
        $poll = $this->testPoll;
        $poll->setEndAt(new \DateTimeImmutable('-1 day'));
        $this->entityManager->flush();

        $this->client->request('GET', '/poll/' . $poll->getShortCode());
        $this->assertResponseIsSuccessful();
    }

    public function testShowValidPoll(): void
    {
        $poll = $this->testPoll;
        
        $this->client->request('GET', '/poll/' . $poll->getShortCode());
        $this->assertResponseIsSuccessful();
    }

    private function resetDatabase(): void
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
        $this->entityManager->clear();
    }

    private function loadFixtures(): void
    {
        $fixtures = new AppFixtures(static::getContainer()->get('security.user_password_hasher'));
        $fixtures->load($this->entityManager);
    }
}
