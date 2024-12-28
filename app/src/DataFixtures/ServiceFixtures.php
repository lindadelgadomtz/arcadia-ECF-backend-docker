<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceFixtures extends Fixture implements ContainerAwareInterface
{
    private ContainerInterface $container;

    public const SERVICE_REFERENCE = 'service_reference_';

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            $service = new Service();
            $service->setNom($faker->company);
            $service->setDescription($faker->text(100));
            $service->setCreatedAt(new \DateTimeImmutable());

            // Persist the service entity
            $manager->persist($service);

            // Store a reference for later use in other fixtures
            $this->addReference(self::SERVICE_REFERENCE . $i, $service);
        }

        // Flush all changes to the database
        $manager->flush();
    }
}
