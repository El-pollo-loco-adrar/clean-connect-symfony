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
        $this->assertNotNull($testUser);
        $client->loginUser($testUser);

        //! 2. RÉCUPÉRATION DES DONNÉES NÉCESSAIRES
        // On les récupère depuis les Fixtures déjà chargées en base de test (WageScale et Skills)

        //$user = $container->get(UserRepository::class)->findOneBy(['email' => 'test-ci@test.com']);
        //$this->assertNotNull($user, "L'utilisateur n'existe pas en base sur GitHub.");

        $wage = $container->get(\App\Repository\WageScaleRepository::class)->findOneBy([]);
        $this->assertNotNull($wage);

        $skill = $container->get(\App\Repository\SkillsRepository::class)->findOneBy([]);
        $this->assertNotNull($skill);

        $area = $container->get(\App\Repository\InterventionAreaRepository::class)->findOneBy(['city' => 'Toulouse']);
        $this->assertNotNull($area);

        //$form['add_mission[areaLocation]'] = (string)$area->getId();

        //! 3. ACCÈS À LA PAGE
        $crawler = $client->request('GET', '/create/mission');
        
        // On vérifie que la page s'affiche
        $this->assertResponseIsSuccessful();

        //! 4. RECUPERATION DU FORMULAIRE
        // On récupère l'objet formulaire via le bouton de soumission
        $form = $crawler->filter('form[name="add_mission"]')->form();

        //! 5. REMPLISSAGE DES CHAMPS
        // Pour les entités (WageScale, Skills), on doit passer l'ID ou l'index.
        $form['title'] = 'Nettoyage Bureaux Test';
        $form['description'] = 'Une description de plus de 10 caractères pour que la validation passe.';
        $form['startAt'] = '2027-01-22T08:00:00';
        $form['endAt'] = '2027-01-22T12:00:00';
        $form['areaLocation'] = '31500 Toulouse';
        $form['wageScale'] = (string)$wage->getId();
        $form['skills'] = [(string)$skill->getId()];

        //! 5. ENVOI ET VÉRIFICATION
        $client->submit($form);
        dump($client->getResponse()->getContent());

        // On attend une redirection vers /home après le succès
        $this->assertResponseRedirects('/show/mission');

        // On suit la redirection pour vérifier que la page d'accueil affiche un message de succès
        //$crawler = $client->followRedirect();
        
        // Si tu as un message flash, on vérifie qu'il est présent
        // $this->assertSelectorExists('.alert-success'); 
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
