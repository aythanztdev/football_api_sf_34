<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Entity\Asset;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class ClubControllerTest extends WebTestCase
{
    use FixturesTrait;

    protected $client;
    private $entityManager;

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

    public function testGetClub()
    {
        $this->client->request('GET', '/api/clubs/1');
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertArrayHasKey('id', $json);
        $this->assertEquals(1, $json['id']);
        $this->assertArrayHasKey('name', $json);
        $this->assertEquals('Real Madrid Club de Fútbol', $json['name']);
        $this->assertArrayHasKey('budget', $json);
        $this->assertEquals(15000000.00, $json['budget']);
    }

    public function testPostClubOk()
    {
        $assetId = $this->createAssetAndGetId();
        $clubArray =
            [
                'name' => 'Club Deportivo Mallorca',
                'budget' => 1000000,
                'shield' => $assetId
            ];

        $this->client->request('POST', '/api/clubs', $clubArray);
        $response = $this->client->getResponse();

        $json = json_decode($response->getContent(), true);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertArrayHasKey('id', $json);
        $this->assertEquals(4, $json['id']);
        $this->assertArrayHasKey('name', $json);
        $this->assertEquals('Club Deportivo Mallorca', $json['name']);
        $this->assertArrayHasKey('budget', $json);
        $this->assertEquals(1000000, $json['budget']);
    }

    private function createAssetAndGetId()
    {
        $asset = new Asset();
        $asset->setPath('/LaLigadeFutbol.jpg');

        $this->entityManager->persist($asset);
        $this->entityManager->flush();

        return $asset->getId();
    }

    /**
     * @throws Exception
     */
    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->loadFixtures([AppFixtures::class]);

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

    }

    protected function tearDown()
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }


}