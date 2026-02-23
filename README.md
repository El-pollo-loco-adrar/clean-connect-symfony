# Projet Symfony + Vue + Tailwind CSS

Ce projet est une application **Symfony** avec un front en **Vue 3**, stylé avec **Tailwind CSS** et bundlé via **Webpack Encore**.

---
## 🚀 Prérequis

Avant de lancer le projet, assure-toi d’avoir installé :

### Backend
- PHP ≥ 8.1
- Composer
- Symfony CLI (recommandé)
- Un serveur de base de données si nécessaire (MySQL / MariaDB / PostgreSQL)

### Frontend
- Node.js ≥ 18
- npm (fourni avec Node.js)

Vérification rapide :
```bash
php -v
composer -V
node -v
npm -v
symfony -v
```

## Modifier le fichier C:\xampp\php\ini
```bash
Enlever le ";" des lignes suivantes :

extension=pdo_mysql
extension=intl
extension=mbstring
```

### Installer les dépendances PHP
```bash
composer install
```

### Installer les dépendances FRONT
```bash
npm install
```

### Installer Tailwind
```bash
php bin/console tailwind:build    
```
## Base de données

1. Créer la bdd
```bash
php bin/console doctrine:database:create   
```
2. Créer fichier de migration
```bash
symfony console make:migration
```
Si erreur "sync-metadata-storage command to fix this issue"
```bash
symfony console doctrine:migrations:sync-metadata-storage
```
3. Lance le fichier migration
```bash
php bin/console doctrine:migrations:migrate
```
4. Créer les rôles dans la table `role`
```bash
Insert into `role` (name_role) value ('ROLE_ADMIN');
insert into `role` (name_role) value ('ROLE_USER');
```
### Installer fixtures et Faker
```bash
composer require --dev orm-fixtures
composer require fakerphp/faker
```

```bash
Modifier le fichier /src/DataFixtures 
```

Charger le fichier de fixtures et l'envoyer en BDD
```bash
symfony console doctrine:fixtures:load ou d :f :l
```

### Installer le bundle DAMA pour effacer la bdd test à chaque test
```bash
composer require --dev dama/doctrine-test-bundle
```
Création de la bdd pour les tests
```bash
# Efface la bdd test
php bin/console doctrine:database:drop --force --env=test

# Crée la bdd test
php bin/console doctrine:database:create --env=test

# Met à jour le schéma
php bin/console doctrine:schema:update --force --env=test

# Charge les fixtures dans la bdd
php bin/console --env=test doctrine:fixtures:load --no-interaction
```
Lancer les test unitaires
```bash
php bin/phpunit
```

### Changer l'adresse de la bdd
```bash
Dans le fichier .env
DATABASE_URL="mysql://root:@127.0.0.1:3306/clean-test?serverVersion=8.0.32&charset=utf8mb4"
```

## ▶️ Lancer le projet en développement
1. Lancer le serveur Symfony
```bash
symfony serve
symfony server:stop
```

2. Lancer le watcher frontend
```bash
npm run watch
```

### Ce watcher compile automatiquement :

Vue

Tailwind

JavaScript

CSS