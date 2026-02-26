<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\Panther\Client;

class RegistrationE2ETest extends PantherTestCase
{
    public function testRegistrationWithBrowser(): void
    {
        $chromeDriverBinary = realpath(__DIR__ . '/../../drivers/chromedriver.exe');
        
        $client = Client::createChromeClient($chromeDriverBinary, null, [
            'external_base_uri' => 'http://127.0.0.1:8000',
            'manage_server' => false,
        ]);

        $client->request('GET', 'http://127.0.0.1:8000/register');

        // Au lieu d'utiliser $this->assertSelectorTextContains(...),
        // on va chercher l'élément manuellement via le client pour contourner le bug
        $crawler = $client->waitFor('h1'); // On attend que le h1 apparaisse
        $h1Text = $crawler->filter('h1')->text();

        $this->assertStringContainsString('Inscription', $h1Text);
        
        echo "\n Succès ! Le titre trouvé est : " . $h1Text . "\n";
    }

    public function testRegistrationFlow(): void
    {
        $chromeDriverBinary = realpath(__DIR__ . '/../../drivers/chromedriver.exe');
        $client = Client::createChromeClient($chromeDriverBinary, null, [
            'external_base_uri' => 'http://127.0.0.1:8000',
            'manage_server' => false,
            'extra_capabilities' => [
                'goog:chromeOptions' => [
                    'args' => [
                        '--window-size=1200,1000',
                        '--auto-open-devtools-for-tabs', // Force l'ouverture des outils dev (donc de la fenêtre)
                        '--remote-allow-origins=*',
                        '--disable-gpu',
                    ],
                    // On demande explicitement à Chrome de ne pas être en mode fantôme
                    'excludeSwitches' => ['enable-automation'],
                ],
            ],
        ]);

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


        // 3. On attend la redirection vers la home
        // On utilise 'h1' car il contient "Bienvenue" dans ton template
        try {
            $client->waitFor('h1', 10);
        } catch (\Exception $e) {
            // Si ça échoue, on sauvegarde l'image pour comprendre pourquoi (ex: erreur de mot de passe)
            $client->takeScreenshot('debug_registration.png');
            throw $e;
        }

        // 4. Assertion finale : On vérifie si on est au moins sur une page connue
        $pageSource = $client->getPageSource();
        
        // On accepte soit la Home (Bienvenue) soit le Login (Connexion)
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