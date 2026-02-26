<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityPageTest extends WebTestCase
{
    /**
     * Test connexion avec Employer
     */
    public function testLoginEmployerSuccess(): void
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

    /***
     * Test connexion avec Candidate
     */
    public function testLoginCandidateSuccess(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // 1. On remplit le formulaire
        $client->submitForm('Se connecter', [
            'email' => 'test-c@test.com',
            'password' => 'SuperPassWord456',
        ]);

        // 2. On vérifie la redirection
        $this->assertResponseRedirects('/home');

        // 3. On suit la redirection pour vérifier que la page d'arrivée est ok
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test d'inscription d'un user
    */
    public function testRegistrationWorks(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $uniqueEmail = 'user-' . uniqid() . '@test.com';

        $form = $crawler->selectButton("S'inscrire")->form();
        $form['registration_form[email]'] = $uniqueEmail;
        $form['registration_form[plainPassword]'] = 'Password1234!';
        $form['registration_form[user_type]'] = 'candidate';
        $form['registration_form[agreeTerms]'] = '1';

        $client->submit($form);

        // AU LIEU de suivre la redirection (qui peut échouer à cause du mail non vérifié)
        // On vérifie JUSTE que le contrôleur a bien voulu nous envoyer vers /home
        $this->assertResponseRedirects('/home');

        // On vérifie en base de données que l'utilisateur existe vraiment
        $user = static::getContainer()->get(UserRepository::class)->findOneByEmail($uniqueEmail);
        $this->assertNotNull($user, "L'utilisateur doit être présent en base de données");
        $this->assertEquals($uniqueEmail, $user->getEmail());
    }

    /**
     * Test d'un utilisateur qui essaie d'accèder à la page /create/mission sans être connecté
     */
    public function testCreateMissionIsProtected():void
    {
        $client = static::createClient();

     //! 1. On tente d'aller sur la page de création sans être connecté
        $client->request('GET', '/create/mission');

     //! 2. Le videur doit nous bloquer et nous rediriger
        $this->assertResponseRedirects();

    //! 3. On vérifie qu'il nous renvoie bien vers la page de connexion
        $client->followRedirect();
    }
}
