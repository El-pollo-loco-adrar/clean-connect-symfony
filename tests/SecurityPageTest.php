<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityPageTest extends WebTestCase
{
    /**
     * Test connexion avec Employer
     */
    public function testAddMissionSuccess(): void
{
    $client = static::createClient();
    $container = static::getContainer();

    // 1. Setup
    $userRepository = $container->get(UserRepository::class);
    $testUser = $userRepository->findOneBy(['email' => 'test-ci@test.com']);
    $client->loginUser($testUser);

    $wage = $container->get(\App\Repository\WageScaleRepository::class)->findOneBy([]);
    $skill = $container->get(\App\Repository\SkillsRepository::class)->findOneBy([]);
    $area = $container->get(\App\Repository\InterventionAreaRepository::class)->findOneBy(['city' => 'Toulouse']);

    // Sécurité CI : On vérifie que skill existe
    $this->assertNotNull($skill, "Aucune compétence trouvée en base.");

    // 2. Requête
    $crawler = $client->request('GET', '/create/mission');
    $form = $crawler->selectButton('Publier la mission')->form();
    
    // 3. Soumission avec Skills
    $client->submit($form, [
        'add_mission[title]' => 'Nettoyage de printemps',
        'add_mission[description]' => 'Une description de plus de dix caracteres pour la validation',
        'add_mission[startAt]' => (new \DateTime('+2 days'))->format('Y-m-d\TH:i'),
        'add_mission[endAt]' => (new \DateTime('+3 days'))->format('Y-m-d\TH:i'),
        'add_mission[areaLocation]' => $area->getPostalCode().' - '.$area->getCity(),
        'add_mission[wageScale]' => (string) $wage->getId(),
        // IMPORTANT : On force le passage en tableau de string
        'add_mission[skills]' => [(string) $skill->getId()], 
    ]);

    // 4. Analyse si ça ne redirige pas (Le débugueur)
    if (!$client->getResponse()->isRedirect()) {
        $crawler = $client->getCrawler();
        // On cherche spécifiquement l'erreur sur le champ skills
        $skillErrors = $crawler->filter('label[for="add_mission_skills"]')->closest('div')->filter('.invalid-feedback')->each(fn($n) => $n->text());
        
        $this->fail("Erreur validation skills : " . implode(' | ', $skillErrors));
    }

    $this->assertResponseRedirects('/show/mission');
}

    /**
     * /
     * Scénario pour la création d'une mission
     */
    public function testAddMissionSuccess(): void
{
    $client = static::createClient();
    $container = static::getContainer();

    // 1. Setup
    $userRepository = $container->get(UserRepository::class);
    $testUser = $userRepository->findOneBy(['email' => 'test-ci@test.com']);
    $client->loginUser($testUser);

    $wage = $container->get(\App\Repository\WageScaleRepository::class)->findOneBy([]);
    $skill = $container->get(\App\Repository\SkillsRepository::class)->findOneBy([]);
    $area = $container->get(\App\Repository\InterventionAreaRepository::class)->findOneBy(['city' => 'Toulouse']);

    // 2. Requête
    $crawler = $client->request('GET', '/create/mission');
    
    // 3. Soumission
    $form = $crawler->selectButton('Publier la mission')->form();
    
    $client->submit($form, [
        'add_mission[title]' => 'Nettoyage de printemps',
        'add_mission[description]' => 'Une description de plus de dix caracteres pour la validation',
        'add_mission[startAt]' => (new \DateTime('+2 days'))->format('Y-m-d\TH:i'),
        'add_mission[endAt]' => (new \DateTime('+3 days'))->format('Y-m-d\TH:i'),
        'add_mission[areaLocation]' => $area->getPostalCode().' - '.$area->getCity(),
        'add_mission[wageScale]' => (string) $wage->getId(),
        'add_mission[skills]' => [(string) $skill->getId()], // Remets le skill ici pour voir
    ]);

    // 4. Assertions finales
    $this->assertResponseRedirects('/show/mission');
    $client->followRedirect();
    $this->assertSelectorTextContains('h1', 'Détails de la mission'); // Ou un titre présent sur ta page de succès
}

    /**
     * Test d'inscription d'un user
    */
    // public function testRegistrationWorks():void
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/register');

    //     $this->assertResponseIsSuccessful();

    //     //! 1. On génère un email unique pour éviter l'erreur "Email déjà utilisé"
    //     // uniqid() génère un identifiant basé sur l'heure actuelle
    //     $uniqueEmail = 'user-' . uniqid() . '@test.com';

    //     //! 2. On récupère le formulaire via le bouton
    //     $form = $crawler->selectButton("S'inscrire")->form([
    //         'registration_form[email]' => $uniqueEmail,
    //         'registration_form[plainPassword]' => 'Password1234!',
    //         'registration_form[user_type]' => 'candidate',
    //         'registration_form[agreeTerms]' => true,
    //     ]);

    //     $client->submit($form);

    //     if ($client->getResponse()->getStatusCode() !== 302) {
    //         // On récupère les erreurs affichées en rouge (text-red-500 dans ton HTML)
    //         $errors = $client->getCrawler()->filter('.text-red-500')->each(fn($n) => $n->text());
    //         echo "\n[ERREUR FORMULAIRE] : " . implode(' | ', $errors) . "\n";
    //     }

    //     //! 3. Vérification de la redirection
    //     $this->assertResponseRedirects('/home');

    //     //! 4. On suit la redirection pour valider
    //     $client->followRedirect();

    //     // /home est protégé, le client est renvoyé vers /
    //     if($client->getResponse()->isRedirect()){
    //         $client->followRedirect();
    //     }

    //     $this->assertResponseIsSuccessful();
    // }

    /**
     * Test d'un utilisateur qui essaie d'accèder à la page /create/mission sans être connecté
     */
    // public function testCreateMissionIsProtected():void
    // {
    //     $client = static::createClient();

    //     //! 1. On tente d'aller sur la page de création sans être connecté
    //     $client->request('GET', '/create/mission');

    //     //! 2. Le videur doit nous bloquer et nous rediriger
    //     $this->assertResponseRedirects();

    //     //! 3. On vérifie qu'il nous renvoie bien vers la page de connexion
    //     $client->followRedirect();
    // }
}
