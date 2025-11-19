<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StructureFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $structures = [
            [
                'name' => 'NDC',
                'description' => '',
                'address' => 'Bamako, Mali',
                'phone' => '+223 76 12 34 56',
                'email' => 'contact@multitadjiso.ml',
                'owner' => 'user_Super' // Référence au Super admin
            ],
            [
                'name' => 'Station Nord',
                'description' => 'Station située dans la zone nord de Bamako',
                'address' => 'Badalabougou, Bamako',
                'phone' => '+223 76 23 45 67',
                'email' => 'nord@multitadjiso.ml',
                'owner' => 'user_Super'
            ],
        ];
        // $manager->flush();
    }
}
