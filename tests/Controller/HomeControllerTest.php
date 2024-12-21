<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HomeControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/');
        
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testPageTitle(): void
    {
        $this->client->request('GET', '/');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'OpenStreamPoll');
    }
}
