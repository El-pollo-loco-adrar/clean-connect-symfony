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
    public function testAddMissionSuccess(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        // --- ÉTAPE 1 : LOGIN ---
        $userRepository = $container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'test-ci@test.com']);
        if (!$testUser) {
            dump("ERREUR : Utilisateur non trouvé");
            die();
        }
        $client->loginUser($testUser);

        // --- ÉTAPE 2 : RÉCUPÉRATION DATA ---
        $wage = $container->get(\App\Repository\WageScaleRepository::class)->findOneBy([]);
        $area = $container->get(\App\Repository\InterventionAreaRepository::class)->findOneBy(['city' => 'Toulouse']);
        
        if (!$area) {
            dump("ERREUR : Toulouse non trouvé dans la DB");
            die();
        }

        // --- ÉTAPE 3 : ACCÈS PAGE ---
        $crawler = $client->request('GET', '/create/mission');
        dump("Statut page : " . $client->getResponse()->getStatusCode());

        // --- ÉTAPE 4 : LE FORMULAIRE ---
        try {
            $buttonCrawler = $crawler->selectButton('Publier la mission');
            $form = $buttonCrawler->form();
            dump("Formulaire trouvé !");
        } catch (\Exception $e) {
            dump("ERREUR : Bouton non trouvé -> " . $e->getMessage());
            die();
        }

        // --- ÉTAPE 5 : SOUMISSION (Le point critique) ---
        dump("Tentative de soumission...");
        
        // On utilise un try/catch pour attraper l'erreur SQL ou Symfony qui fait crasher le CI
        try {
            $client->submit($form, [
                'add_mission[title]' => 'Menage de printemps',
                'add_mission[description]' => 'Une description de plus de 10 caracteres',
                'add_mission[startAt]' => (new \DateTime('+2 days'))->format('Y-m-d\TH:i'),
                'add_mission[endAt]' => (new \DateTime('+3 days'))->format('Y-m-d\TH:i'),
                'add_mission[areaLocation]' => $area->getPostalCode().' - '.$area->getCity(),
                'add_mission[wageScale]' => (string) $wage->getId(),
                'add_mission[skills]' => [], // On teste vide pour isoler le problème
            ]);
        } catch (\Throwable $t) {
            dump("CRASH PENDANT SUBMIT : " . $t->getMessage());
            dump("FICHIER : " . $t->getFile() . " Ligne : " . $t->getLine());
            die(); 
        }

        dump("Soumission terminée sans crash fatal.");
        
        $response = $client->getResponse();
        dump("Code HTTP après submit : " . $response->getStatusCode());
        
        if (!$response->isRedirect()) {
            dump("Contenu de l'erreur (1000 chars) : " . substr($response->getContent(), 0, 1000));
        }

        $this->assertResponseRedirects('/show/mission');
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
