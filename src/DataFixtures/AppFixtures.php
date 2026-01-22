<?php

namespace App\DataFixtures;

use App\Entity\Employer;
use App\Entity\WageScale;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Role;
use App\Entity\SkillCategory;
use App\Entity\Skills;
use App\Entity\Mission;
use App\Entity\InterventionArea;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {
        //Mise en place du faker
        $faker = Faker\Factory::create("fr_FR");
        
        //création des rôles
        $adminRole = new Role();
        $adminRole->setNameRole('ROLE_ADMIN');
        $manager->persist($adminRole);

        $userRole = new Role();
        $userRole->setNameRole('ROLE_USER');
        $manager->persist($userRole);

        //création d'un user pour les tests
        $testUser = new Employer();
        $testUser->setFirstname('Test')
                ->setLastname('User')
                ->setCompanyName('Compagnie de test')
                ->setEmail('test-ci@test.com')
                ->setRole($userRole);
        $password = $this->hasher->hashPassword($testUser,'SuperPassWord123');
        $testUser->setPassword($password);

        $manager->persist($testUser);

        //création d'un ADMIN
        $admin = new User();
        $password = $this->hasher->hashPassword($admin,'AdminPassword123');
        $admin->setFirstname('Super')
            ->setLastname('Admin')
            ->setEmail('admin@admin.com')
            ->setRole($adminRole)
            ->setIsVerified(true)
            ->setPassword($password);
        
            $manager->persist($admin);

        //création de user
        $users = [];
        for($i = 0; $i<100; $i++){
            $user = new User();
            $user->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setEmail($faker->unique->email())
                ->setPassword($faker->password())
                ->setPhoneNumber($faker->unique->phoneNumber())
                ->setRole($userRole);

            $users[]= $user;
            $manager->persist($user);
        }
        //Création d'une zone pour les tests
        $testArea = new InterventionArea();
        $testArea->setCity('Toulouse')
                ->setPostalCode('31500');

        $manager->persist($testArea);

        //Création des zone d'interventions
        $areas = [];
        for($i = 0; $i< 10; $i++){
            $area = new InterventionArea();
            $area->setCity($faker->unique->city())
                ->setPostalCode($faker->postcode());
            
                $areas[] = $area;
                $manager->persist($area);
        }

        //Création de missions
        $missions = [];
        for($i = 0; $i< 100; $i++){
            $mission = new Mission();
            $randomArea = $faker->randomElement($areas);

            $mission->setTitle($faker->jobTitle())
                ->setDescription($faker->sentence(15))
                ->setStartAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('now', '+1 month')))
                ->setEndAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('+1 month', '+2 months')))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')))
                ->setAreaLocation($randomArea);

            $missions[]= $mission;
            $manager->persist($mission);
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
