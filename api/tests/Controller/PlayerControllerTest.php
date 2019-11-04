<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class PlayerControllerTest extends WebTestCase
{
    use FixturesTrait;

    protected $client;

    public function testGetPlayers()
    {
        $this->client->request('GET', '/api/players');
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertCount(16, $json);
        foreach ($json as $key => $value) {
            $this->assertArrayHasKey('id', $json[$key]);
            $this->assertArrayHasKey('name', $json[$key]);
            $this->assertArrayHasKey('email', $json[$key]);
            $this->assertArrayHasKey('birthday', $json[$key]);
            $this->assertArrayHasKey('position', $json[$key]);
            $this->assertArrayHasKey('type', $json[$key]);
            $this->assertArrayHasKey('salary', $json[$key]);
            $this->assertArrayHasKey('club', $json[$key]);
        }
    }

    public function testGetPlayer()
    {
        $this->client->request('GET', '/api/players/1');
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertArrayHasKey('id', $json);
        $this->assertEquals(1, $json['id']);
        $this->assertArrayHasKey('name', $json);
        $this->assertEquals('Eden Hazard', $json['name']);
        $this->assertArrayHasKey('email', $json);
        $this->assertEquals('testcllfp+1@gmail.com', $json['email']);
        $this->assertArrayHasKey('birthday', $json);
        $this->assertEquals('07-01-1991', $json['birthday']);
        $this->assertArrayHasKey('position', $json);
        $this->assertEquals('FORWARD', $json['position']);
        $this->assertArrayHasKey('type', $json);
        $this->assertEquals('PROFESSIONAL', $json['type']);
        $this->assertArrayHasKey('salary', $json);
        $this->assertEquals(1500000, $json['salary']);
        $this->assertArrayHasKey('club', $json);
        $this->assertEquals(1, $json['club']['id']);
    }

    public function testPostPlayerOk()
    {
        $playerArray =
                        [
                            'name' =>  'Sergio Ramos',
                            'email' => 'testcllfp+20@gmail.com',
                            'birthday' => '30-03-1986',
                            'position' => 'DEFENDER',
                            'type' => 'PROFESSIONAL',
                            'salary' => 100000,
                            'club' => 1
                        ];

        $this->client->request('POST', '/api/players', $playerArray);
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertArrayHasKey('id', $json);
        $this->assertEquals(17, $json['id']);
        $this->assertArrayHasKey('name', $json);
        $this->assertEquals('Sergio Ramos', $json['name']);
        $this->assertArrayHasKey('email', $json);
        $this->assertEquals('testcllfp+20@gmail.com', $json['email']);
        $this->assertArrayHasKey('birthday', $json);
        $this->assertEquals('30-03-1986', $json['birthday']);
        $this->assertArrayHasKey('position', $json);
        $this->assertEquals('DEFENDER', $json['position']);
        $this->assertArrayHasKey('type', $json);
        $this->assertEquals('PROFESSIONAL', $json['type']);
        $this->assertArrayHasKey('salary', $json);
        $this->assertEquals(100000, $json['salary']);
        $this->assertArrayHasKey('club', $json);
        $this->assertEquals(1, $json['club']['id']);
    }

    public function testPostPlayerKoForSalaryGreaterThanBudget()
    {
        $playerArray =
            [
                'name' => 'Sergio Ramos',
                'email' => 'testcllfp@gmail.com',
                'birthday' => '30-03-1986',
                'position' => 'DEFENDER',
                'type' => 'PROFESSIONAL',
                'salary' => 100000000,
                'club' => 1
            ];

        $this->client->request('POST', '/api/players', $playerArray);
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $json);
        $this->assertArrayHasKey('errors', $json['errors']);
        $this->assertContains('Total salaries cannot be greater than', $json['errors']['errors'][0]);
    }

    public function testPostPlayerKoForProfessionalTotalPlayerCannotBeGreaterThanFivePerClub()
    {
        $playerArray =
            [
                'name' => 'Sergio Ramos',
                'email' => 'testcllfp@gmail.com',
                'birthday' => '30-03-1986',
                'position' => 'DEFENDER',
                'type' => 'PROFESSIONAL',
                'salary' => 10000,
                'club' => 3
            ];

        $this->client->request('POST', '/api/players', $playerArray);
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $json);
        $this->assertArrayHasKey('errors', $json['errors']);
        $this->assertContains('Total players per team cannot be greater than', $json['errors']['errors'][0]);
    }

    public function testPostPlayerKoForJuniorPlayerCannotBeOlderThanTwentyThree()
    {
        $playerArray =
            [
                'name' => 'Sergio Ramos',
                'email' => 'testcllfp@gmail.com',
                'birthday' => '30-03-1986',
                'position' => 'DEFENDER',
                'type' => 'JUNIOR',
                'club' => 3
            ];

        $this->client->request('POST', '/api/players', $playerArray);
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $json);
        $this->assertArrayHasKey('errors', $json['errors']);
        $this->assertContains('Junior player must not be more than 23 years old', $json['errors']['errors'][0]);
    }

    public function testPostPlayerKoForJuniorPlayerCannotHaveSalary()
    {
        $playerArray =
            [
                'name' => 'Sergio Ramos',
                'email' => 'testcllfp@gmail.com',
                'birthday' => '30-03-2000',
                'position' => 'DEFENDER',
                'type' => 'JUNIOR',
                'salary' => 123,
                'club' => 3
            ];

        $this->client->request('POST', '/api/players', $playerArray);
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $json);
        $this->assertArrayHasKey('errors', $json['errors']);
        $this->assertContains('Salary must be blank or 0 for junior players', $json['errors']['errors'][0]);
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