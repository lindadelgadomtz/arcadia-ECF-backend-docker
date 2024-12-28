<?php

namespace App\DataFixtures;

use App\Entity\Race;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RaceFixtures extends Fixture
{
    public const RACE_REFERENCE = 'race_reference_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            $race = new Race();
            $race->setLabel($faker->unique()->word);

            // Persist the race entity
            $manager->persist($race);

            // Store a reference for later use in other fixtures
            $this->addReference(self::RACE_REFERENCE . $i, $race);
        }

        // Flush all changes to the database
        $manager->flush();
    }
}
