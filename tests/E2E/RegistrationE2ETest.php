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

        $client->request('GET', 'http://127.0.0.1:8000/register');

        $crawler = $client->waitFor('h1');
        $h1Text = $crawler->filter('h1')->text();

        $this->assertStringContainsString('Inscription', $h1Text);
        
        echo "\n Succès ! Le titre trouvé est : " . $h1Text . "\n";
    }

    public function testRegistrationFlow(): void
    {
        $client = $this->createCustomClient();

        $crawler = $client->request('GET', 'http://127.0.0.1:8000/register');

        // 1. On attend que le formulaire soit visible
        $client->waitFor('form[name="registration_form"]');

        // 2. On remplit les champs
        $client->submitForm("S'inscrire", [
            'registration_form[email]' => 'e2e-browser-' . uniqid() . '@test.com',
            'registration_form[plainPassword]' => 'Password123!',
            'registration_form[user_type]' => 'candidate',
            'registration_form[agreeTerms]' => true,
        ]);

        sleep(3);

        // 3. On attend la redirection
        try {
            $client->waitFor('h1', 10);
        } catch (\Exception $e) {
            $client->takeScreenshot('debug_registration.png');
            throw $e;
        }

        // 4. Assertion finale
        $pageSource = $client->getPageSource();
        $isHome = str_contains($pageSource, 'Bienvenue');
        $isLogin = str_contains($pageSource, 'Connexion');

        $this->assertTrue($isHome || $isLogin, "Le navigateur n'est ni sur la Home ni sur le Login.");

        if ($isLogin) {
            echo "\n Inscription réussie, redirection vers Connexion. \n";
        } else {
            echo "\n Inscription réussie, redirection vers Accueil. \n";
        }
    }
}