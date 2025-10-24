<?php
// src/DataFixtures/UserRoleFixtures.php

namespace App\DataFixtures;

use App\Entity\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserRoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roles = [
            [
                'name' => 'SUPER_ADMIN',
                'description' => 'Accès complet à tout le système'
            ],
            [
                'name' => 'ADMIN',
                'description' => 'Administrateur de structure'
            ],
            [
                'name' => 'GERANT',
                'description' => 'Gestionnaire de stations'
            ],
        ];

        foreach ($roles as $roleData) {
            $role = new UserRole();
            $role->setName($roleData['name']);
            $role->setDescription($roleData['description']);

            $manager->persist($role);
            $this->addReference('ROLE_' . $roleData['name'], $role, UserRole::class);
        }

        $manager->flush();
    }
}
