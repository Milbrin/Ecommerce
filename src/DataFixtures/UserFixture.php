<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Faker;


class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        // generate data by calling methods
        
        for ($i = 0; $i <= 10; $i++) {
            $user = new User();
            $user->setFirstname($faker->firstname());
            $user->setLastname($faker->lastname());
            $user->setEmail($faker->email());
            $user->setPassword($faker->password());
            $user->setPhone($faker->phoneNumber());
            $user->setLine1($faker->streetAddress());
            $user->setLine2($faker->secondaryAddress());
            $user->setLine3($faker->secondaryAddress());
            $user->setCity($faker->city());
            $user->setZipCode($faker->randomNumber(5, true));
            $user->setCountry($faker->country());
            $user->setCreatedAt(new \DatetimeImmutable());
            $user->setUpdatedAt(new \DatetimeImmutable());
            $manager->persist($user);
        }

        $manager->flush();
    }
}
