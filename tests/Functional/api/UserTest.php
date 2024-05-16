<?php

namespace Functional\api;

use App\DataFixtures\AppFixtures;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class UserTest extends WebTestCase
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
    public function testGetUsers(): void
    {
        $this->client->request('GET', '/api/users');

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
    }

    /**
     * @return void
     */
    public function testCreateUser(): void
    {
        $this->client->request('POST', '/api/users', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name'  => 'User name',
            'email' => 'user_name@example.com',
            'group_id' => 1
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertJson( $this->client->getResponse()->getContent());
        $this->assertEquals(201,  $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGetUsersByCategory(): void
    {
        $this->client->request('GET', '/api/groups/1/users');

        $this->assertResponseIsSuccessful();
        $this->assertJson( $this->client->getResponse()->getContent());
        $this->assertEquals(200,  $this->client->getResponse()->getStatusCode());

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
    }

    /**
     * @return void
     */
    public function testGetUserById(): void
    {
        $this->client->request('GET', '/api/users/1');

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
    }

    /**
     * @return void
     */
    public function testDeleteUser(): void
    {
        $this->client->request('DELETE', '/api/users/1');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateUser(): void
    {
        $this->client->request('PUT', '/api/users/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name'  => 'New User name',
            'email' => 'new_user_name@example.com',
//            'group_id' => 1
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