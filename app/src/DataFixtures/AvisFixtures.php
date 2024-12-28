<?php

namespace App\DataFixtures;

use App\Entity\Avis;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AvisFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create an array of sample data for the Avis entity
        $avisData = [
            ['pseudo' => 'JohnDoe', 'commentaire' => 'Great zoo! Loved the animals.', 'isVisible' => true],
            ['pseudo' => 'JaneSmith', 'commentaire' => 'Had a fantastic time with the family.', 'isVisible' => true],
            ['pseudo' => 'AnimalLover', 'commentaire' => 'The enclosures are well-maintained.', 'isVisible' => false],
            ['pseudo' => 'Visitor123', 'commentaire' => 'A bit crowded but still enjoyable.', 'isVisible' => true],
            ['pseudo' => 'ZooFanatic', 'commentaire' => 'Best zoo experience ever!', 'isVisible' => true],
        ];

        // Loop through the sample data and create Avis entities
        foreach ($avisData as $data) {
            $avis = new Avis();
            $avis->setPseudo($data['pseudo'])
                 ->setCommentaire($data['commentaire'])
                 ->setIsVisible($data['isVisible']);

            // Persist each Avis entity to the database
            $manager->persist($avis);
        }

        // Flush all changes to the database
        $manager->flush();
    }
}
