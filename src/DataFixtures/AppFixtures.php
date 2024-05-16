<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 *
 */
class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $userGroupOne = new Group();
        $userGroupOne->setName('Group ONE');
        $manager->persist($userGroupOne);

        $userGroupTwo = new Group();
        $userGroupTwo->setName('Group TWO');
        $manager->persist($userGroupTwo);

        $userGroupThree = new Group();
        $userGroupThree->setName('Group THREE');
        $manager->persist($userGroupThree);

        for ($i = 0; $i < 5; $i++) {
            $user = new User();

            $user->setName('user_' . $i);
            $user->setEmail('some_email_' . $i . '@herdwatch.com');
            $user->setGroup($userGroupOne);

            $manager->persist($user);
        }

        for ($i = 5; $i < 10; $i++) {
            $user = new User();

            $user->setName('user_' . $i);
            $user->setEmail('some_email_' . $i . '@herdwatch.com');
            $user->setGroup($userGroupTwo);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
