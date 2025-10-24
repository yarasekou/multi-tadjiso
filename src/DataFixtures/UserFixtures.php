<?php
// src/DataFixtures/UserFixtures.php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            // Super Admin
            [
                'firstname' => 'Super',
                'lastname' => 'Admin',
                'email' => 'superadmin@multitadjiso.ml',
                'password' => 'superadmin123',
                'phone' => '+223 76 12 34 56',
                'address' => 'Bamako, Mali',
                'level' => 1,
                'roles' => ['SUPER_ADMIN'],
                'enable' => true
            ]
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setFirstname($userData['firstname']);
            $user->setLastname($userData['lastname']);
            $user->setEmail($userData['email']);
            $user->setPhone($userData['phone']);
            $user->setAddress($userData['address']);
            $user->setLevel($userData['level']);
            $user->setEnable($userData['enable']);
            $user->setCreatedAt(new \DateTimeImmutable());
            // Hasher le mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);

            foreach ($userData['roles'] as $roleName) {
                $role = $this->getReference('ROLE_' . $roleName, UserRole::class);
                $user->addUserRole($role);
            }

            $manager->persist($user);

            $this->addReference('user_' . str_replace(' ', '_', $userData['firstname']), $user, User::class);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserRoleFixtures::class,
        ];
    }
}
