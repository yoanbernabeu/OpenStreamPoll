<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Entity\Poll;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->resetDatabase();
        $this->loadFixtures();
        $this->loginAsAdmin();
    }

    public function testIndexPageIsSecured(): void
    {
        $this->client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShowPollPage(): void
    {
        $poll = $this->entityManager->getRepository(Poll::class)->findOneBy(['title' => 'Active Poll']);
        $this->client->request('GET', '/admin/show/'.$poll->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreatePollPage(): void
    {
        // Supprimer d'abord tous les sondages pour éviter le conflit de sondage actif
        $polls = $this->entityManager->getRepository(Poll::class)->findAll();
        foreach ($polls as $poll) {
            $this->entityManager->remove($poll);
        }
        $this->entityManager->flush();

        $this->client->request('GET', '/admin/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testEditPollPage(): void
    {
        // Créer un nouveau sondage sans votes pour le test
        $newPoll = new Poll();
        $newPoll
            ->setTitle('Test Poll')
            ->setDescription('Test Description')
            ->setShortCode('test123')
            ->setStartAt(new \DateTimeImmutable('-1 hour'))
            ->setEndAt(new \DateTimeImmutable('+1 hour'))
            ->setQuestion1('Choice 1')
            ->setQuestion2('Choice 2');

        $this->entityManager->persist($newPoll);
        $this->entityManager->flush();

        $this->client->request('GET', '/admin/edit/'.$newPoll->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testResultsPage(): void
    {
        $poll = $this->entityManager->getRepository(Poll::class)->findOneBy(['title' => 'Active Poll']);
        $this->client->request('GET', '/admin/_results/'.$poll->getId());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCannotCreatePollWhenActiveExists(): void
    {
        $this->client->request('GET', '/admin/create');
        $this->assertResponseRedirects('/admin');
    }

    public function testCannotEditPollWithVotes(): void
    {
        $poll = $this->entityManager->getRepository(Poll::class)->findOneBy(['title' => 'Active Poll']);
        $this->client->request('GET', '/admin/edit/'.$poll->getId());
        $this->assertResponseRedirects('/admin/show/'.$poll->getId());
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

    private function loginAsAdmin(): void
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Sign in', [
            '_username' => 'admin',
            '_password' => 'adminpass',
        ]);
        // S'assurer que la redirection est suivie
        $this->client->followRedirect();
    }

    private function generateCsrfToken(string $id): string
    {
        return static::getContainer()->get('security.csrf.token_manager')->getToken($id)->getValue();
    }
}
