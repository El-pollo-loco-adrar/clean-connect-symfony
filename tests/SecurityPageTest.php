<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityPageTest extends WebTestCase
{
    /**
     * Test connexion avec Employer
     */
    public function testLoginSuccess(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/'); 

        // 1. On remplit et soumet le formulaire
        $client->submitForm('Se connecter', [
            'email' => 'test-ci@test.com',
            'password' => 'SuperPassWord123',
        ]);

        // 2. On vérifie la redirection
        // Si ça échoue ici, PHPUnit affichera quand même une erreur détaillée
        $this->assertResponseRedirects('/home');

        // 3. On suit la redirection pour vérifier que la page d'arrivée est OK
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    /**
     * /
     * Scénario pour la création d'une mission
     */
// public function testAddMissionSuccess(): void
// {
//     $client = static::createClient();
//     $container = static::getContainer();

//     // 1. Authentification
//     $userRepository = $container->get(UserRepository::class);
//     $testUser = $userRepository->findOneBy(['email' => 'test-ci@test.com']);
//     $client->loginUser($testUser);

//     // 2. Data
//     $wage = $container->get(\App\Repository\WageScaleRepository::class)->findOneBy([]);
//     $skill = $container->get(\App\Repository\SkillsRepository::class)->findOneBy([]);
//     $area = $container->get(\App\Repository\InterventionAreaRepository::class)->findOneBy(['city' => 'Toulouse']);

//     // 3. Récupération du formulaire
//     $crawler = $client->request('GET', '/create/mission');
//     $form = $crawler->selectButton('Publier la mission')->form();

//     // 4. On prépare les valeurs
//     $values = $form->getPhpValues(); // On récupère les valeurs par défaut (dont le Token CSRF !)

//     // On injecte manuellement nos données dans le tableau
//     $values['add_mission']['title'] = 'Menage Pro';
//     $values['add_mission']['description'] = 'Une description de plus de dix caractères';
//     $values['add_mission']['startAt'] = (new \DateTime('+7 days'))->format('Y-m-d\TH:i');
//     $values['add_mission']['endAt'] = (new \DateTime('+8 days'))->format('Y-m-d\TH:i');
//     $values['add_mission']['areaLocation'] = $area->getPostalCode().' - '.$area->getCity();
//     $values['add_mission']['wageScale'] = (string) $wage->getId();
    
//     // Pour les compétences (EntityType multiple + expanded), Symfony attend un tableau d'IDs
//     $values['add_mission']['skills'] = [(string) $skill->getId()];

//     // 5. Soumission forcée
//     $client->request($form->getMethod(), $form->getUri(), $values);

//     // 6. Vérification
//     $this->assertResponseRedirects('/show/mission');
// }

    /**
     * Test d'inscription d'un user
    */
    public function testRegistrationWorks():void
{
    $client = static::createClient();
    $crawler = $client->request('GET', '/register');

    $this->assertResponseIsSuccessful();

    $uniqueEmail = 'user-' . uniqid() . '@test.com';

    // 1. On sélectionne le bouton et on récupère l'objet Form
    $buttonCrawlerNode = $crawler->selectButton("S'inscrire");
    $form = $buttonCrawlerNode->form();

    // 2. On remplit les champs via l'objet form
    // On utilise les ID ou les noms des champs
    $form['registration_form[email]'] = $uniqueEmail;
    $form['registration_form[plainPassword]'] = 'Password1234!';
    $form['registration_form[user_type]'] = 'candidate';
    $form['registration_form[agreeTerms]'] = '1';

    // 3. On soumet l'objet form (il inclut le jeton CSRF tout seul !)
    $client->submit($form);

    // 4. On vérifie la redirection
    // Note : Vérifie si ta route est '/home' ou '/app_home' (l'URL, pas le nom de la route)
    $this->assertResponseRedirects('/');

    $client->followRedirect();

    // 2. Optionnel : On vérifie qu'il y a un message flash de succès
    // (Adapte le sélecteur CSS selon ton template, souvent .alert-success)
    $this->assertSelectorExists('.fixed.top-5.right-5');
    
    // 3. Optionnel : Vérifier en BDD que l'utilisateur est bien là mais non vérifié
    $user = static::getContainer()->get(UserRepository::class)->findOneByEmail($uniqueEmail);
    $this->assertNotNull($user);
    $this->assertFalse($user->isVerified());
    $this->assertResponseIsSuccessful();
}

    /**
     * Test d'un utilisateur qui essaie d'accèder à la page /create/mission sans être connecté
     */
//     public function testCreateMissionIsProtected():void
//     {
//         $client = static::createClient();

//      //! 1. On tente d'aller sur la page de création sans être connecté
//         $client->request('GET', '/create/mission');

//      //! 2. Le videur doit nous bloquer et nous rediriger
//         $this->assertResponseRedirects();

//     //! 3. On vérifie qu'il nous renvoie bien vers la page de connexion
//         $client->followRedirect();
//     }
}
