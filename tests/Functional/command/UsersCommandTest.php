<?php

namespace Functional\command;

use App\DataFixtures\AppFixtures;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class UsersCommandTest extends KernelTestCase
{
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
    public function testGetUsersCommand()
    {
        // Boot the Symfony kernel
        self::bootKernel();

        // Get the application object
        $application = new Application(self::$kernel);

        $command = $application->find('app:get-users');
        $commandTester = new CommandTester($command);;

        // Execute the command
        $commandTester->execute([]);

        // Assert the output
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('user_3', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGetUserCommand()
    {
        // Boot the Symfony kernel
        self::bootKernel();

        // Get the application object
        $application = new Application(self::$kernel);

        $command = $application->find('app:get-user');
        $commandTester = new CommandTester($command);;

        // Execute the command
        $commandTester->execute(['--userId' => 1]);

        // Assert the output
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('user_0', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRemoveUserCommand()
    {
        // Boot the Symfony kernel
        self::bootKernel();

        // Get the application object
        $application = new Application(self::$kernel);

        $command = $application->find('app:remove-user');
        $commandTester = new CommandTester($command);;

        // Execute the command
        $commandTester->execute(['--userId' => 1]);

        // Assert
        $commandTester->getDisplay();

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testCreateUserCommand()
    {
        // Boot the Symfony kernel
        self::bootKernel();

        // Get the application object
        $application = new Application(self::$kernel);

        $command = $application->find('app:create-user');
        $commandTester = new CommandTester($command);;

        // Execute the command
        $commandTester->execute([
            '--userName' => 'Some new name',
            '--userEmail' => 'someNewEmail@gmail.com',
            '--userGroup' => 1,
        ]);

        // Assert
        $commandTester->getDisplay();

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateUserCommand()
    {
        // Boot the Symfony kernel
        self::bootKernel();

        // Get the application object
        $application = new Application(self::$kernel);

        $command = $application->find('app:update-user');
        $commandTester = new CommandTester($command);;

        // Execute the command
        $commandTester->execute([
            '--userId'    => 1,
            '--userName'  => 'Some new name',
            '--userEmail' => 'someNewEmail@gmail.com',
            '--userGroup' => 1,
        ]);

        // Assert
        $commandTester->getDisplay();

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGetUsersOfGroupCommand()
    {
        // Boot the Symfony kernel
        self::bootKernel();

        // Get the application object
        $application = new Application(self::$kernel);

        $command = $application->find('app:get-users-of-group');
        $commandTester = new CommandTester($command);;

        // Execute the command
        $commandTester->execute([
            '--groupId'    => 1,
        ]);

        // Assert
        $commandTester->getDisplay();

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
