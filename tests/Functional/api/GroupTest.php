<?php

namespace Functional\api;

use App\DataFixtures\AppFixtures;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class GroupTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    private $client;

    /**
     * @var
     */
    private $entityManager;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();

        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        //Recreating database
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        //Loading fixtures
        $fixtures = new AppFixtures();
        $fixtures->load($this->entityManager);
    }

    /**
     * @return void
     */
    public function testGetGroups(): void
    {
        $this->client->request('GET', '/api/groups');

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
    }

    /**
     * @return void
     */
    public function testCreateGroup(): void
    {
        $this->client->request('POST', '/api/groups', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name'  => 'New group',
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertJson( $this->client->getResponse()->getContent());
        $this->assertEquals(201,  $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGetGroupById(): void
    {
        $this->client->request('GET', '/api/groups/1');

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
    }

    /**
     * @return void
     */
    public function testDeleteGroup(): void
    {
        $this->client->request('DELETE', '/api/groups/3');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateGroup(): void
    {
        $this->client->request('PUT', '/api/groups/3', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name'  => 'New Group name',
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertJson( $this->client->getResponse()->getContent());
        $this->assertEquals(200,  $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}