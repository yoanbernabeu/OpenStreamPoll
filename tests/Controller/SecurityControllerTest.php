<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManagerInterface;

class SecurityControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        
        // Réinitialisation de la base de données avant chaque test
        $this->resetDatabase();
        
        // Chargement des fixtures une seule fois dans le setUp
        $this->loadFixtures();
    }

    public function testLoginPageIsAccessible(): void
    {
        $this->client->request('GET', '/login');
        
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
    }

    public function testLoginWithBadCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            '_username' => 'wrong_user',
            '_password' => 'wrong_password',
        ]);
        
        $this->client->submit($form);
        
        $this->assertResponseRedirects('/login');
        
        $this->client->followRedirect();
        
        $this->assertSelectorExists('.alert.alert-error');
    }

    public function testLogoutRedirectsToLogin(): void
    {
        $this->client->request('GET', '/logout');
        
        $this->assertResponseRedirects();
    }

    public function testSuccessfulLogin(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            '_username' => 'admin',
            '_password' => 'adminpass',
        ]);
        
        $this->client->submit($form);
        
        $this->assertResponseRedirects();
    }

    public function testLogoutWhenAuthenticated(): void
    {
        // Connexion
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            '_username' => 'admin',
            '_password' => 'adminpass',
        ]);
        
        $this->client->submit($form);
        $this->client->followRedirect();
        
        // Vérification que l'utilisateur est bien connecté
        $this->client->request('GET', '/login');
        $this->assertSelectorTextContains('.alert.alert-info', 'You are logged in as admin');
        
        // Déconnexion
        $this->client->request('GET', '/logout');
        $this->assertResponseRedirects();
        
        $this->client->followRedirect();
        $this->assertSelectorNotExists('.alert.alert-info');
    }

    private function resetDatabase(): void
    {
        // Drop puis recrée le schéma
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
        
        // Clear l'entity manager
        $this->entityManager->clear();
    }

    private function loadFixtures(): void
    {
        $fixtures = new AppFixtures(self::getContainer()->get('security.user_password_hasher'));
        $fixtures->load($this->entityManager);
    }
}
