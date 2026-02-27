<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\Panther\Client;

class RegistrationE2ETest extends PantherTestCase
{
    /**
     * Cette fonction crée un client adapté à l'OS (Windows ou Linux)
     */
    private function createCustomClient(): Client
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            // Config pour ton PC Windows (Chrome)
            $chromeDriverBinary = realpath(__DIR__ . '/../../drivers/chromedriver.exe');
            return Client::createChromeClient($chromeDriverBinary, null, [
                'external_base_uri' => 'http://127.0.0.1:8000',
                'manage_server' => false,
                'extra_capabilities' => [
                    'goog:chromeOptions' => [
                        'args' => [
                            '--window-size=1200,1000',
                            '--remote-allow-origins=*',
                            '--disable-gpu',
                        ],
                    ],
                ],
            ]);
        }

        // Config pour ton PC Linux Debian ou GitHub Actions (Firefox)
        // On laisse null car geckodriver est dans /usr/local/bin
        return Client::createFirefoxClient(null, null, [
            'external_base_uri' => 'http://127.0.0.1:8000',
            'manage_server' => false,
        ]);
    }

    public function testRegistrationWithBrowser(): void
    {
        $client = $this->createCustomClient();
        $crawler = $client->request('GET', 'http://127.0.0.1:8000/register');

        // On vérifie simplement la présence du titre. 
        // Si ça échoue, Panther lancera une exception de toute façon.
        $this->assertSelectorTextContains('h1', 'Inscription');
    }

    // public function testRegistrationWithBrowser(): void
    // {
    //     $client = $this->createCustomClient();

    //     $crawler = $client->request('GET', 'http://127.0.0.1:8000/register');

    //     // On vérifie tout de suite si on a un H1, même si c'est une erreur
    //     $h1Text = $crawler->filter('h1')->count() > 0 ? $crawler->filter('h1')->text() : 'Pas de H1';

    //     // Si on tombe sur une page d'erreur Symfony
    //     if (str_contains($h1Text, 'Exception') || str_contains($h1Text, 'Error')) {
    //         echo "\n --- ERREUR AU CHARGEMENT DE LA PAGE REGISTER --- \n";
    //         try {
    //             echo "MESSAGE : " . $crawler->filter('.exception-message')->text() . "\n";
    //         } catch (\Exception $e) {
    //             echo "DÉTAIL INTROUVABLE. Voici le body : \n" . substr($crawler->filter('body')->text(), 0, 300) . "\n";
    //         }
            
    //         // On force le screenshot de l'erreur
    //         $client->takeScreenshot(getcwd() . '/error_load_register.png');
    //     }

    //     // Si c'est bien la bonne page, ça passera
    //     $this->assertStringContainsString('Inscription', $h1Text, "La page n'a pas chargé correctement.");
    // }

    public function testRegistrationFlow(): void
    {
        $client = $this->createCustomClient();
        $client->request('GET', 'http://127.0.0.1:8000/register');

        $client->waitFor('form[name="registration_form"]');

        $client->submitForm("S'inscrire", [
            'registration_form[email]' => 'e2e-browser-' . uniqid() . '@test.com',
            'registration_form[plainPassword]' => 'Password123!',
            'registration_form[user_type]' => 'candidate',
            'registration_form[agreeTerms]' => true,
        ]);

        // On attend la redirection vers login ou home
        // Panther réessaiera pendant 30s par défaut
        $client->waitForVisibility('h1'); 
        
        $pageSource = $client->getPageSource();
        $isHome = str_contains($pageSource, 'Bienvenue');
        $isLogin = str_contains($pageSource, 'Connexion');

        // On garde une assertion claire
        $this->assertTrue($isHome || $isLogin, "L'inscription n'a pas redirigé vers l'accueil ou la connexion.");
    }
}