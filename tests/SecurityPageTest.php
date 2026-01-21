<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityPageTest extends WebTestCase
{
    public function testRegisterPageIsUp(): void
    {
        $client = static::createClient();
        // On simule une visite sur la page d'inscription
        $client->request('GET', '/register');

        // On vérifie que la page répond "200 OK"
        $this->assertResponseIsSuccessful();

        // On vérifie que le titre contient bien "Inscription"
        $this->assertSelectorTextContains('h1', 'Inscription');
    }

    public function testLoginPageIsUp(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    // public function testRegistrationWorks(): void
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/register');

    //     // On sélectionne le bouton "S'inscrire" (par son texte)
    //     $buttonCrawlerNode = $crawler->selectButton('S\'inscrire');

    //     // On récupère le formulaire lié à ce bouton
    //     $form = $buttonCrawlerNode->form([
    //         'registration_form[email]' => 'test-unitaire@test.com',
    //         'registration_form[plainPassword]' => 'CorrectPassword123456',
    //         'registration_form[user_type]' => 'candidate',
    //         'registration_form[agreeTerms]' => true,
    //     ]);

    //     // On soumet le formulaire
    //     $client->submit($form);

    //     // On vérifie qu'on est redirigé (souvent vers la home après inscription)
    //     $this->assertResponseRedirects('/home');
        
    //     // On suit la redirection pour vérifier le message de succès si tu en as un
    //     $client->followRedirect();
    // }

    // public function testLoginWorks(): void
    // {
    //     $client = static::createClient();
    //     $client->request('GET', '/');

    //     // On remplit le formulaire avec les identifiants créés dans AddFixtures
    //     $client->submitForm('Se connecter', [
    //         'email'=> 'test-ci@test.com',
    //         'password'=> 'SuperPassWord123',
    //     ]);

    //     //  On vérifie que la connexion redirige vers /home
    //     $this->assertResponseRedirects('/home');

    //     // On suit la redirection pour vérifier la connexion
    //     $client->followRedirect();
    // }

    // public function testAddMission(): void
    // {
    //     $client = static::createClient();
    //     $container = static::getContainer();
    //     $entityManager = $container->get('doctrine.orm.entity_manager');

    //     // --- AJOUT POUR LA CONNEXION ---
    //     $userRepository = $container->get(UserRepository::class);
    //     $testUser = $userRepository->findOneBy(['email' => 'test-ci@test.com']);
    //     $client->loginUser($testUser);

    //     //Je récupère les données pour skill et wageScale
    //     $wage = $container->get(\App\Repository\WageScaleRepository::class)->findOneBy([]);
    //     $skill = $container->get(\App\Repository\SkillsRepository::class)->findOneBy([]);
        
    //     $crawler = $client->request('GET', '/create/mission');

    //     $this->assertResponseIsSuccessful();

    //     // On sélectionne le bouton "Publier la mission" (par son texte)
    //     $buttonCrawlerNode = $crawler->selectButton('Publier la mission');

    //     // On récupère le formulaire lié à ce bouton
    //     $form = $buttonCrawlerNode->form([
    //         'add_mission[title]'=> 'Mission test',
    //         'add_mission[description]'=> 'Mission test et description pour tester tout ça',
    //         'add_mission[startAt]'=> '2027-01-22T08:00',
    //         'add_mission[endAt]'=> '2027-01-22T12:00',
    //         'add_mission[areaLocation]'=> '31500 - Toulouse',
    //         'add_mission[wageScale]'=> $wage->getId(),
    //         'add_mission[skills]'=> [$skill->getId()],
    //     ]);
    //     $client->submit($form);
    //     $this->assertResponseRedirects('/home');
    // }
    
}
