<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->freeEmail);
            $user->setPassword($faker->password);
            $user->setNom($faker->name);
            $user->setPrenom($faker->firstName);
            $user->setAdresse($faker->streetAddress);
            $user->setCodePostal($faker->postcode);
            $user->setVille($faker->city);
            $user->setTelephone($faker->phoneNumber);
            $user->setPseudo($faker->unique()->userName);
            $user->setSexe($faker->randomElement(['homme', 'femme']));
            $user->setDatenaissance($faker->dateTimeThisCentury($max = '2004', $timezone = null));
            $user->setCreatedAt(new DateTimeImmutable());
            $user->setupdatedAt(new DateTime());
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
