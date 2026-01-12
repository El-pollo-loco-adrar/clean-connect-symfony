<?php

namespace App\DataFixtures;

use App\Entity\WageScale;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Role;
use App\Entity\SkillCategory;
use App\Entity\Skills;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //Mise en place du faker
        $faker = Faker\Factory::create("fr_FR");
        
        //création des rôles
        $adminRole = new Role();
        $adminRole->setNameRole('admin');
        $manager->persist($adminRole);

        
        $userRole = new Role();
        $userRole->setNameRole('user');
        $manager->persist($userRole);


        //création de user
        $users = [];
        for($i = 0; $i<100; $i++){
            $user = new User();
            $user->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setEmail($faker->email())
                ->setPassword($faker->password())
                ->setPhoneNumber($faker->phoneNumber())
                ->setRole($userRole);

        $users[]= $user;
        $manager->persist($user);
        }

        //Création des skills category
        $categoriesData = [
            'Entretient courant',
            'Entretient mécanisé',
            'Remise en état'
        ];

        $categories = [];
        foreach($categoriesData as $id => $name) {
            $category = new SkillCategory();
            $category->setNameCategory($name);
            $manager->persist($category);

            $categories[$id +1] = $category;
        }

        //Création des skills
        $skillsData = [
            ['name' => 'Bureau', 'skill_category_id' => 1],
            ['name' => 'Décapage', 'skill_category_id' => 3],
            ['name' => 'Lavage Mécanisé', 'skill_category_id' => 2],
            ['name' => 'Lustrage', 'skill_category_id' => 2],
            ['name' => 'Sanitaire', 'skill_category_id' => 1],
            ['name' => 'Shampoing moquette', 'skill_category_id' => 3],
            ['name' => 'Sol', 'skill_category_id' => 1],
            ['name' => 'Spray', 'skill_category_id' => 2],
            ['name' => 'Vitres', 'skill_category_id' => 1],
        ];

        foreach($skillsData as $data) {
            $skill = new Skills();
            $skill->setNameSkill($data['name']);
            $skill->setSkillCategory($categories[$data['skill_category_id']]);
            $manager->persist($skill);
        }

        //Ajout du taux horaire (wage scale)
        $wageScale = [
            ['niveau'=> 'ATQS', 'level' => 3, 'hourlyRate' => 14.79],
            ['niveau'=> 'ATQS', 'level' => 2, 'hourlyRate' => 13.76],
            ['niveau'=> 'ATQS', 'level' => 1, 'hourlyRate' => 13.03],
            ['niveau'=> 'AQS', 'level' => 3, 'hourlyRate' => 12.78],
            ['niveau'=> 'AQS', 'level' => 2, 'hourlyRate' => 12.67],
            ['niveau'=> 'AQS', 'level' => 1, 'hourlyRate' => 12.56],
            ['niveau'=> 'AS', 'level' => 3, 'hourlyRate' => 12.50],
            ['niveau'=> 'AS', 'level' => 2, 'hourlyRate' => 12.43],
            ['niveau'=> 'AS', 'level' => 1, 'hourlyRate' => 12.38],
        ];
        foreach($wageScale as $data){
            $wageScale = new WageScale();
            $wageScale->setNiveau($data['niveau']);
            $wageScale->setLevel($data['level']);
            $wageScale->setHourlyRate($data['hourlyRate']);
            $manager->persist($wageScale);
        }

        $manager->flush();
    }
}
