<?php

namespace Functional\command;

use App\DataFixtures\AppFixtures;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GroupsCommandTest extends KernelTestCase
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
    public function testGetGroupsCommand()
    {
        // Boot the Symfony kernel
        self::bootKernel();

        // Get the application object
        $application = new Application(self::$kernel);

        $command = $application->find('app:get-groups');
        $commandTester = new CommandTester($command);;

        // Execute the command
        $commandTester->execute([]);

        // Assert the output
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Group ONE', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGetGroupCommand()
    {
        // Boot the Symfony kernel
        self::bootKernel();

        // Get the application object
        $application = new Application(self::$kernel);

        $command = $application->find('app:get-group');
        $commandTester = new CommandTester($command);;

        // Execute the command
        $commandTester->execute(['--groupId' => 1]);

        // Assert the output
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Group ONE', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testRemoveGroupCommand()
    {
        // Boot the Symfony kernel
        self::bootKernel();

        // Get the application object
        $application = new Application(self::$kernel);

        $command = $application->find('app:remove-group');
        $commandTester = new CommandTester($command);;

        // Execute the command
        $commandTester->execute(['--groupId' => 1]);

        // Assert
        $commandTester->getDisplay();

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testCreateGroupCommand()
    {
        // Boot the Symfony kernel
        self::bootKernel();

        // Get the application object
        $application = new Application(self::$kernel);

        $command = $application->find('app:create-group');
        $commandTester = new CommandTester($command);;

        // Execute the command
        $commandTester->execute([
            '--groupName' => 'Some new name',
        ]);

        // Assert
        $commandTester->getDisplay();

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    /**
     * @return void
     */
    public function testUpdateGroupCommand()
    {
        // Boot the Symfony kernel
        self::bootKernel();

        // Get the application object
        $application = new Application(self::$kernel);

        $command = $application->find('app:update-group');
        $commandTester = new CommandTester($command);;

        // Execute the command
        $commandTester->execute([
            '--groupId'    => 1,
            '--groupName'  => 'Some new name',
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
