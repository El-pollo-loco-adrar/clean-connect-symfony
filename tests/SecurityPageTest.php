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

        //! 1. CONNEXION
        $userRepository = $container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'test-ci@test.com']);
        $this->assertNotNull($testUser, "L'utilisateur test-ci@test.com est introuvable.");
        $client->loginUser($testUser);

        //! 2. RÉCUPÉRATION DES DONNÉES
        $wage = $container->get(\App\Repository\WageScaleRepository::class)->findOneBy([]);
        $skill = $container->get(\App\Repository\SkillsRepository::class)->findOneBy([]);
        $area = $container->get(\App\Repository\InterventionAreaRepository::class)->findOneBy(['city' => 'Toulouse']);
        
        $this->assertNotNull($area, "Zone 'Toulouse' non trouvée.");

        //! 3. ACCÈS À LA PAGE
        $crawler = $client->request('GET', '/create/mission');
        $this->assertResponseIsSuccessful();

        //! 4. RÉCUPÉRATION ET REMPLISSAGE DU FORMULAIRE
        // On sélectionne le bouton, ce qui permet à Symfony de gérer le jeton CSRF automatiquement
        $form = $crawler->selectButton('Publier la mission')->form();

        // On remplit les champs via l'accès tableau (plus fiable que de passer un array à submit)
        $form['add_mission[title]'] = 'Nettoyage Bureaux Test';
        $form['add_mission[description]'] = 'Une description de plus de dix caractères pour la validation.';
        $form['add_mission[startAt]'] = (new \DateTime('+2 days'))->format('Y-m-d\TH:i');
        $form['add_mission[endAt]'] = (new \DateTime('+3 days'))->format('Y-m-d\TH:i');
        $form['add_mission[areaLocation]'] = $area->getPostalCode().' - '.$area->getCity();
        $form['add_mission[wageScale]'] = (string) $wage->getId();
        $form['add_mission[skills]'] = [(string) $skill->getId()];

        //! 5. ENVOI
        $client->submit($form);

        //! 6. VÉRIFICATION
        $response = $client->getResponse();
        
        if (!$response->isRedirect()) {
            // En cas d'échec, on affiche les erreurs de formulaire réelles
            $errors = $client->getCrawler()->filter('.invalid-feedback, .alert-danger')->each(fn($n) => $n->text());
            $this->fail("Le formulaire n'a pas redirigé. Erreurs trouvées : " . implode(' | ', $errors));
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
