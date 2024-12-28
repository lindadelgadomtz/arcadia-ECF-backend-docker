<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Sample users data
        $usersData = [
            [
                'email' => 'admin@arcadiazoo.com',
                'password' => 'password123',
                'roles' => ['ROLE_MANAGER'],
            ],
            [
                'email' => 'vet@arcadiazoo.com',
                'password' => 'password123',
                'roles' => ['ROLE_VETERINAIRE'],
            ],
            [
                'email' => 'employee@arcadiazoo.com',
                'password' => 'password123',
                'roles' => ['ROLE_EMPLOYEE'],
            ],
        ];

        foreach ($usersData as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);

            // Hash the password before setting it
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);

            // Set the createdAt and updatedAt dates
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());

            // Persist the user entity
            $manager->persist($user);
        }

        // Flush all changes to the database
        $manager->flush();
    }
}
