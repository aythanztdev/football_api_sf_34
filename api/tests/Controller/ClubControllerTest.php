<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class ClubControllerTest extends WebTestCase
{
    use FixturesTrait;

    protected $client;

    public function testGetClubs()
    {
        $this->client->request('GET', '/api/clubs');
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertCount(3, $json);
        foreach ($json as $key => $value) {
            $this->assertArrayHasKey('id', $json[$key]);
            $this->assertArrayHasKey('name', $json[$key]);
            $this->assertArrayHasKey('budget', $json[$key]);
        }
    }

    /**
     * @throws \Exception
     */
    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->loadFixtures([AppFixtures::class]);

    }
}