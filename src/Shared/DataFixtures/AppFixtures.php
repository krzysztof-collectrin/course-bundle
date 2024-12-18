<?php

namespace CourseBundle\Shared\DataFixtures;

use CourseBundle\User\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne(['password' => 'password123'])->getPassword();
    }
}
