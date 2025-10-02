<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->createUser($manager);
    }

    private function createUser(ObjectManager $manager) :void
    {
        $user = new User();
        $user->setFirstname('splint');
        $user->setLastname('Gurby');
        $user->setEmail('splint@test.fr');
        $user->setPassword('123');
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);
        $manager->flush();
    }
}
