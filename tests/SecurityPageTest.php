<?php

namespace App\Tests;

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
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testRegistrationWorks(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        // On sélectionne le bouton "S'inscrire" (par son texte)
        $buttonCrawlerNode = $crawler->selectButton('S\'inscrire');

        // On récupère le formulaire lié à ce bouton
        $form = $buttonCrawlerNode->form([
            'registration_form[email]' => 'test-unitaire@test.com',
            'registration_form[plainPassword]' => 'CorrectPassword123456',
            'registration_form[user_type]' => 'candidate',
            'registration_form[agreeTerms]' => true,
        ]);

        // On soumet le formulaire
        $client->submit($form);

        // On vérifie qu'on est redirigé (souvent vers la home après inscription)
        $this->assertResponseRedirects('/home');
        
        // On suit la redirection pour vérifier le message de succès si tu en as un
        $client->followRedirect();
    }

    // public function testLoginWorks(): void
    // {
    //     $client = static::createClient();
    //     $client->request('GET', '/login');

    //     // On remplit le formulaire avec les identifiants créés au test précédent
    //     $client->submitForm('Se connecter', [
    //         'email'=> 'test-unitaire@test.com',
    //         'password'=> 'CorrectPassword123456',
    //     ]);

    //     //  On vérifie que la connexion redirige vers /home
    //     $this->assertResponseRedirects('/home');

    //     // On suit la redirection pour vérifier la connexion
    //     $client->followRedirect();
    //te
    // }
    
}
