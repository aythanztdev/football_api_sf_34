<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class CoachControllerTest extends WebTestCase
{
    use FixturesTrait;

    protected $client;

    public function testGetCoaches()
    {
        $this->client->request('GET', '/api/coaches');
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertCount(3, $json);
        foreach ($json as $key => $value) {
            $this->assertArrayHasKey('id', $json[$key]);
            $this->assertArrayHasKey('name', $json[$key]);
            $this->assertArrayHasKey('email', $json[$key]);
            $this->assertArrayHasKey('salary', $json[$key]);
            $this->assertArrayHasKey('club', $json[$key]);
        }
    }

    public function testGetCoach()
    {
        $this->client->request('GET', '/api/coaches/1');
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertArrayHasKey('id', $json);
        $this->assertEquals(1, $json['id']);
        $this->assertArrayHasKey('name', $json);
        $this->assertEquals('Zinedine Zidane', $json['name']);
        $this->assertArrayHasKey('email', $json);
        $this->assertEquals('testcllfp@gmail.com', $json['email']);
        $this->assertArrayHasKey('salary', $json);
        $this->assertEquals(1000000, $json['salary']);
        $this->assertArrayHasKey('club', $json);
        $this->assertEquals(1, $json['club']['id']);
    }

    public function testPostCoachOk()
    {
        $coachArray =
            [
                'name' => 'Javi Moreno',
                'email' => 'testcllfp@gmail.com',
                'salary' => 1000,
                'club' => 1
            ];

        $this->client->request('POST', '/api/coaches', $coachArray);
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertArrayHasKey('id', $json);
        $this->assertEquals(4, $json['id']);
        $this->assertArrayHasKey('name', $json);
        $this->assertEquals('Javi Moreno', $json['name']);
        $this->assertArrayHasKey('email', $json);
        $this->assertEquals('testcllfp@gmail.com', $json['email']);
        $this->assertArrayHasKey('salary', $json);
        $this->assertEquals(1000, $json['salary']);
        $this->assertArrayHasKey('club', $json);
        $this->assertEquals(1, $json['club']['id']);
    }

    public function testPostCoachKoForSalaryGreaterThanBudget()
    {
        $coachArray =
            [
                'name' => 'Javi Moreno',
                'email' => 'testcllfp@gmail.com',
                'salary' => 100000000,
                'club' => 1
            ];

        $this->client->request('POST', '/api/coaches', $coachArray);
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $json);
        $this->assertArrayHasKey('errors', $json['errors']);
        $this->assertContains('Total salaries cannot be greater than', $json['errors']['errors'][0]);
    }

    /**
     * @throws Exception
     */
    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->loadFixtures([AppFixtures::class]);

    }
}