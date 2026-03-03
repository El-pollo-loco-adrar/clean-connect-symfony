<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

/**
 * C'est un fichier très important, même si on ne le touche presque jamais. Pour faire simple : le bootstrap.php, c'est le "bouton d'allumage" de ton environnement de test.

*Voici précisément ses deux rôles :
*1. Charger les variables d'environnement (Dotenv)

*C'est ce bloc de code qui dit à PHPUnit : "Avant de lancer le moindre test, va lire le fichier .env (et .env.test, .env.test.local, etc.)".
*Sans ce fichier, tes tests ne sauraient pas quel est le nom de ta base de données ou quel est ton APP_SECRET. C'est lui qui crée le lien entre tes fichiers de configuration et tes tests.
*2. Gérer les droits d'écriture (umask)

*Le petit bloc à la fin :
*PHP

*if ($_SERVER['APP_DEBUG']) {
   *umask(0000);
*

*ert à s'assurer que les fichiers créés pendant les tests (comme le cache ou les logs) sont accessibles en lecture et écriture pour tout le monde. Sans ça, tu pourrais avoir des erreurs de type "Permission Denied" quand tu lances tes tests, car le cache créé par la console ne pourrait pas être lu par le serveur web (ou inversement).
*🔍 Pourquoi est-il là ?

*PHPUnit est un outil indépendant de Symfony. Quand tu lances bin/phpunit, il ne sait pas par défaut qu'il travaille sur un projet Symfony. PHPUnit regarde alors son fichier de configuration (phpunit.xml.dist) qui contient cette ligne :
*XML

*<phpunit bootstrap="tests/bootstrap.php" ...>

*C'est cette ligne qui ordonne à PHPUnit de toujours exécuter bootstrap.php en premier.
*💡 Est-ce que tu dois le modifier ?

*Généralement, non. La seule raison de le modifier serait si tu avais besoin d'exécuter un script très spécifique avant chaque session de test (par exemple, vider un dossier temporaire spécifique ou pré-charger des données en mémoire).

*Pour ton projet "Fil Rouge", ce fichier est parfait tel quel. Il garantit que ton APP_ENV passe bien à test et que toute ta configuration est chargée.
 */